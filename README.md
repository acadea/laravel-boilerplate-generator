# Laravel Boilerplate Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/acadea/laravel-boilerplate-generator.svg?style=flat-square)](https://packagist.org/packages/acadea/laravel-boilerplate-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/acadea/laravel-boilerplate-generator/run-tests?label=tests)](https://github.com/acadea/laravel-boilerplate-generator/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/acadea/laravel-boilerplate-generator.svg?style=flat-square)](https://packagist.org/packages/acadea/laravel-boilerplate-generator)

An opinionated boilerplate generator. Generate boilerplates like repositories, routes, events, api docs and much more!

## NOTE

This project is still under development and unusable.

## Installation

You can install the package via composer:

```bash
composer require acadea/boilerplate
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Acadea\Boilerplate\BoilerplateServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Acadea\Boilerplate\BoilerplateServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

First, define a `schema.php` file in the `database` folder. You can overwrite the default file path in
the `boilerplate.php` config file.

### Structure of `schema.php`

Must follow convention
Pivot: post_tag

Model name must be singular 

```php

return [
    'post' => [
        'title' => [
            // any column type supported by eloquent
            // https://laravel.com/docs/8.x/migrations#available-column-types
            'type' => 'string', 
            // attributes are column modifier  
            // https://laravel.com/docs/8.x/migrations#column-modifiers
            'attributes' => [
                // put a flat string if no argument to pass to the modifier
                'nullable',  
                // if we need to pass arguments to the modifier
                // array key is the modifier method, value should be an array of arguments value to pass to the modifier
                'default' => ['some post'],   
            ], 
        ],
        'body' => [
            'type' => 'mediumText',
            'attributes' => ['nullable'],
        ],
        'book_author_id' => [
            'type' => 'foreignId',
            'foreign' => [
                'references' => 'id',
                'on' => 'book_authors',
            ],
        ],
        // will add belongsToMany relationship to model
        'tags' => [
            'type' => 'pivot',
            'pivot' => [
                'table' => 'post_tag',

            ]
        ]

    ],
    
    // PIVOT TABLE
    // add 'pivot:' before table name to create pivot migration
    'pivot:post_tag' => [
        'post_id' => [
            // to set this column as primary key in the pivot table
            'primary' => true,
            'type' => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign' => [
                'references' => 'id',
                'on' => 'posts',
            ],
        ],
        'tag_id' => [
            'primary' => true,
            'type' => 'foreignId',
            'attributes' => [
                'index'
            ],
            'foreign' => [
                'references' => 'id',
                'on' => 'tags',
            ],
        ],

    ],
];
```

``` php
$skeleton = new Acadea\Boilerplate();
echo $skeleton->echoPhrase('Hello, Acadea!');
```

## Caveats



## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@acadea.be instead of using the issue tracker.

## Credits

- [Sam Ngu](https://github.com/sam-ngu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
