<?php


namespace Acadea\Boilerplate\Commands\Traits\Model;

use Acadea\Boilerplate\Utils\SchemaStructure;
use Illuminate\Support\Str;

trait ReplaceRelations
{
    /**
     * find models where field has foreign on current model
     * @param string $modelName
     */
    private function getRelatedHasManyModels(string $modelName)
    {
        $structures = SchemaStructure::get();

        $filtered = collect($structures)->filter(function ($schema, $modelKey) use ($modelName) {
            if (substr($modelKey, 0, 6) === 'pivot:') {
                return false;
            }
            $foreignKeyFields = collect($schema)
                ->filter(function ($fields, $schemaName) {
                    return data_get($fields, 'foreign');
                });

            // check the 'on' is related to current model
            $ons = data_get($foreignKeyFields, '*.foreign.on');

            return collect($ons)->first(fn ($on) => Str::plural(Str::snake($modelName)) === Str::plural($on));
        });

        return $filtered->keys();
    }

    private function generateRelationMethod($relationName, $relationMethod, $relatedModelName, ...$args)
    {
        $args = collect($args)->join('\',\'');

        return 'public function ' . $relationName . '()' .
            '{' .
            '    return $this->' . $relationMethod . '(\\App\\Models\\' . Str::studly($relatedModelName) . '::class, \'' . $args . '\');' .
            '}';
    }

    private function generateBelongsTo($fieldName, $relationName)
    {
        // fieldname should be snakecase
        $foreignKey = Str::snake(Str::camel($fieldName));
        $exploded = explode('_', $foreignKey);
        // get rid of last element, which is usually 'id'
//        $relationName = implode('_', array_slice($exploded, 0, sizeof($exploded) - 1));
        $modelName = Str::studly(Str::singular(Str::camel($relationName)));
        $relationName = Str::snake(Str::camel($modelName));

        return $this->generateRelationMethod($relationName, 'belongsTo', $modelName, $foreignKey);
    }

    private function generateHasMany($foreignKey)
    {
        $foreignKey = Str::snake(Str::camel($foreignKey));
        $modelName = Str::studly($foreignKey);
        $relationName = Str::plural($foreignKey);

        return $this->generateRelationMethod($relationName, 'hasMany', $modelName, $foreignKey);
    }

    private function generateBelongsToMany($relationName, $tableName, $foreignKey, $relatedKey)
    {
        $relatedKey = Str::snake(Str::camel($relatedKey));

        $modelName = Str::studly(Str::singular(Str::camel($relationName)));

        return $this->generateRelationMethod(Str::plural($relationName), 'belongsToMany', $modelName, $tableName, $foreignKey, $relatedKey);
    }

    protected function replaceRelations($stub, $name)
    {
        $fields = $this->getModelFields($name);

        // generateHasMany
        // $hasManyModels is an array of models with hasMany relationship to the current model
        $hasManyModels = $this->getRelatedHasManyModels($name);

        $hasManyRelationMethods = collect($hasManyModels)->map(function ($model) {
            return $this->generateHasMany($model);
        });

        // generate belongsToMany
        // find models where field has pivot
        $belongsToManyRelationMethods = collect($fields)->filter(function ($field) {
            return data_get($field, 'type') === 'pivot';
        })->map(function ($field, $fieldName) {
            $tableName = data_get($field, 'pivot.table');
            $relatedKey = data_get($field, 'pivot.related_key');
            $foreignKey = data_get($field, 'pivot.foreign_key');

            return $this->generateBelongsToMany($fieldName, $tableName, $foreignKey, $relatedKey);
        });

        $belongsToRelationMethods = collect($fields)->filter(function ($field) {
            return data_get($field, 'foreign');
        })->map(function ($field, $fieldName) use ($name) {
            // fieldName looks something like author_id
            return $this->generateBelongsTo($fieldName, data_get($field, 'foreign.on'));
        });

        $relations = $hasManyRelationMethods
            ->concat($belongsToRelationMethods)
            ->concat($belongsToManyRelationMethods);

        return str_replace(['{{ relations }}', '{{relations}}'], $relations->join("\n"), $stub);
    }
}
