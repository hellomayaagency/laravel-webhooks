
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# Laravel package to simpify the receipt, storage and handling of webhooks

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hellomayaagency/laravel-webhooks.svg?style=flat-square)](https://packagist.org/packages/hellomayaagency/laravel-webhooks)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/hellomayaagency/laravel-webhooks/run-tests?label=tests)](https://github.com/hellomayaagency/laravel-webhooks/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/hellomayaagency/laravel-webhooks/Check%20&%20fix%20styling?label=code%20style)](https://github.com/hellomayaagency/laravel-webhooks/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hellomayaagency/laravel-webhooks.svg?style=flat-square)](https://packagist.org/packages/hellomayaagency/laravel-webhooks)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require hellomayaagency/laravel-webhooks
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="webhooks-migrations"
php artisan migrate
```

You should publish the config file with:

```bash
php artisan vendor:publish --tag="webhooks-config"
```

This file is used to configure which job classes handle which received webhooks

## Usage

```php
$laravelWebhooks = new Hellomayaagency\LaravelWebhooks();
echo $laravelWebhooks->echoPhrase('Hello, Hellomayaagency!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Andrew Ellender](https://github.com/hellomayaagency)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
