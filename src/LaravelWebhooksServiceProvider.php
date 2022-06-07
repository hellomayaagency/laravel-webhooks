<?php

namespace Hellomayaagency\LaravelWebhooks;

use Hellomayaagency\LaravelWebhooks\Commands\LaravelWebhooksCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWebhooksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-webhooks')
            ->hasConfigFile('laravel-webhooks')
            ->hasMigration('create_laravel_webhooks_table');
    }
}
