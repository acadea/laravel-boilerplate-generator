<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Utils\SchemaStructure;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ModelMakeCommand extends \Illuminate\Foundation\Console\ModelMakeCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boilerplate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eloquent model class with loaded boilerplate.';


    /**
     * Location of the file
     * @param string $name
     * @return string
     */
    public function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }


    /** Workaround to bypass ModelMakeCommand handle function
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handleInit()
    {
        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $content = $this->buildClass($name);

        $content = $this->replaceFillables($content, $this->getNameInput());

        $content = $this->replaceCasts($content, $this->getNameInput());

        $content = $this->replaceRelations($content, $this->getNameInput());

        $this->files->put($path, Fixer::format($this->sortImports($content)));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->handleInit() === false && ! $this->option('force')) {
            return ;
        }

        if ($this->option('all')) {
            $name = Str::studly(class_basename($this->argument('name')));

            // create resource class
            $this->call('make:resource', [
                'name' => $name.'Resource',
            ]);


            // generate event classes if passed --event flag
            $eventClasses = ['Created', 'PermanentlyDeleted', 'Updated', 'Restored', 'Deleted'];
            collect($eventClasses)->each(function ($class) use ($name) {
                Artisan::call('boilerplate:api-event', [
                    'name' => $name . $class,
                    '--model' => $name,
                ]);
            });

            // create repo
            $this->call('boilerplate:repository', [
                'name' => $name.'Repository',
            ]);

            // create routes
            $this->call('boilerplate:route', [
                'name' => $name,
            ]);
        }

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('controller') || $this->option('resource') || $this->option('api')) {
            $this->createController();

            // create requests files
        }
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly(class_basename($this->argument('name')));

        $this->call('boilerplate:factory', [
            'name' => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('boilerplate:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createSeeder()
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('boilerplate:seed', [
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call('boilerplate:controller', array_filter([
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') || $this->option('api') ? $modelName : null,
            '--api' => $this->option('api'),
        ]));
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__. '/..' . $stub;
    }


    protected function getModelFields($modelName)
    {
        return data_get(SchemaStructure::get(), strtolower(Str::singular($modelName)));
    }

    protected function replaceFillables($stub, $name)
    {
        // generate the fields
        $fields = array_keys($this->getModelFields($name));

        $fillables = collect($fields)->map(function ($field) {
            return '\'' . $field . '\'';
        });

        return str_replace(['{{ fillables }}', '{{fillables}}'], $fillables->join(','), $stub);
    }

    protected function replaceCasts($stub, $name)
    {
        $fields = $this->getModelFields($name);

        // filter timestamp and json fields
        $fields = collect($fields)->filter(function ($field) {
            // check if field is date time or json
            $type = data_get($field, 'type');

            return  $type === 'timestamp' || $type === 'json';
        })->map(function ($field, $fieldName) {
            $cast = data_get($field, 'type') === 'json' ? 'array' : 'timestamp';

            return '\'' . $fieldName . '\' => \'' . $cast . '\'';
        });

        return str_replace(['{{ casts }}', '{{casts}}'], $fields->join(','), $stub);
    }

    protected function replaceRelations($stub, $name)
    {
        $fields = $this->getModelFields($name);

        $generateRelationMethod = function ($relationName, $relationMethod, $relatedModelName, $foreignKey){
            return 'public function ' . $relationName . '()' .
                '{' .
                '    return $this->'.$relationMethod.'(\\App\\Models\\'.Str::studly($relatedModelName).'::class, \''. $foreignKey .'\');' .
                '}';
        };

        $generateBelongsTo = function ($fieldName) use ($generateRelationMethod){
            // fieldname should be snakecase
            $fieldName = Str::snake(Str::camel($fieldName));
            $exploded = explode('_', $fieldName);
            // get rid of last element, which is usually 'id'
            $relationName = implode('_', array_slice($exploded, 0, sizeof($exploded) - 1));
            $modelName = Str::studly($relationName);
            return $generateRelationMethod($relationName, 'belongsTo', $modelName, $fieldName);
        };

        $generateHasMany = function ($fieldName) use($generateRelationMethod){
            $fieldName = Str::snake(Str::camel($fieldName));
            $modelName = Str::studly($fieldName);
            $relationName = Str::plural($fieldName);
            return $generateRelationMethod($relationName, 'hasMany', $modelName, $fieldName);

        };

        // generateHasMany
        // $hasManyModels is an array of models with hasMany relationship to the current model
        $hasManyModels = $this->getRelatedHasManyModels($name);

        $hasManyRelationMethods = collect($hasManyModels)->map(function ($model) use ($generateHasMany){
            return $generateHasMany($model);
        });

        // TODO: generate belongsToMany
        // find models where field has pivot

        $belongsToRelationMethods = collect($fields)->filter(function ($field) {
            return data_get($field, 'foreign');
        })->map(function ($field, $fieldName) use ($generateBelongsTo, $name) {
            // fieldName looks something like author_id
            return $generateBelongsTo($fieldName);
        });

        $relations = $hasManyRelationMethods->concat($belongsToRelationMethods);

        return str_replace(['{{ relations }}', '{{relations}}'], $relations->join("\n"), $stub);
    }

    /**
     * find models where field has foreign on current model
     * @param string $modelName
     */
    private function getRelatedHasManyModels(string $modelName)
    {
        $structures = SchemaStructure::get();

        $filtered = collect($structures)->filter(function ($schema, $modelKey) use ($modelName) {
            $foreignKeyFields = collect($schema)->filter(fn ($field)=> data_get($field, 'foreign'));

            // check the 'on' is related to current model
            $ons = data_get($foreignKeyFields, '*.foreign.on');

            return collect($ons)->first(fn($on) => Str::plural(Str::snake($modelName)) === Str::plural($on));
        });

        return $filtered->keys();
    }
}
