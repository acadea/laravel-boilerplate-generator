# Laravel Boilerplate Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/acadea/laravel-boilerplate-generator.svg?style=flat-square)](https://packagist.org/packages/acadea/laravel-boilerplate-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/acadea/laravel-boilerplate-generator/run-tests?label=tests)](https://github.com/acadea/laravel-boilerplate-generator/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/acadea/laravel-boilerplate-generator.svg?style=flat-square)](https://packagist.org/packages/acadea/laravel-boilerplate-generator)


An opinionated boilerplate generator. Generate boilerplates like repositories, routes, events, api docs and much more!


## Installation

You can install the package via composer:

```bash
composer require acadea/package-skeleton-laravel
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

``` php
$skeleton = new Acadea\Boilerplate();
echo $skeleton->echoPhrase('Hello, Acadea!');
```

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
