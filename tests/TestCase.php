<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

use Hellomayaagency\LaravelWebhooks\LaravelWebhooksServiceProvider;
use Illuminate\Support\Facades\Bus;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelWebhooksServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function defineDatabaseMigrations(): void
    {
        $migration = include __DIR__.'/../database/migrations/create_laravel_webhooks_table.php.stub';

        $migration->up();
    }
}
