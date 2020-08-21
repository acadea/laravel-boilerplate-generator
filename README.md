
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

First, define a `schema.php` file in the `database` folder. You can overwrite the default file path in the `boilerplate.php` config file. 

### Structure of `schema.php`
```
```

``` php
$skeleton = new Acadea\Boilerplate();
echo $skeleton->echoPhrase('Hello, Acadea!');
```

## Caveats
1. `hasMany` Relationship is not loaded to the boilerplate


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
