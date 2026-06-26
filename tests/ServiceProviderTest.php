<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_package_configuration(): void
    {
        $this->assertSame(
            \Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook::class,
            config('laravel-webhooks.model')
        );

        $this->assertIsArray(config('laravel-webhooks.jobs'));
    }
}
