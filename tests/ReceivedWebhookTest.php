<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

use Hellomayaagency\LaravelWebhooks\Exceptions\WebhookFailed;
use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;
use Hellomayaagency\LaravelWebhooks\Tests\Support\TestWebhookHandler;
use Illuminate\Support\Facades\Bus;

class ReceivedWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TestWebhookHandler::$handledWebhook = null;
    }

    /** @test */
    public function it_saves_exception_when_type_is_missing(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => '',
            'payload' => ['foo' => 'bar'],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->process();

        $webhook->refresh();

        $this->assertNotNull($webhook->exception);
        $this->assertStringContainsString('did not contain a type', $webhook->exception['message']);
        Bus::assertNothingDispatched();
    }

    /** @test */
    public function it_saves_exception_when_type_is_not_configured(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'unknown_type',
            'payload' => ['type' => 'unknown_type'],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->process();

        $webhook->refresh();

        $this->assertNotNull($webhook->exception);
        $this->assertStringContainsString('no configured jobclass', $webhook->exception['message']);
        Bus::assertNothingDispatched();
    }

    /** @test */
    public function it_saves_exception_when_job_class_does_not_exist(): void
    {
        config()->set('laravel-webhooks.jobs.default.missing_handler', '\\App\\Jobs\\NonExistentHandler');

        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'missing.handler',
            'payload' => ['type' => 'missing.handler'],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->process();

        $webhook->refresh();

        $this->assertNotNull($webhook->exception);
        $this->assertStringContainsString('does not exist', $webhook->exception['message']);
        Bus::assertNothingDispatched();
    }

    /** @test */
    public function it_dispatches_configured_job_for_valid_webhook(): void
    {
        config()->set('laravel-webhooks.jobs.default.test_event', TestWebhookHandler::class);

        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test.event',
            'payload' => ['type' => 'test.event', 'data' => 'value'],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->process();

        Bus::assertDispatched(TestWebhookHandler::class, function (TestWebhookHandler $job) use ($webhook) {
            return $job->getWebhook()->is($webhook);
        });
    }

    /** @test */
    public function it_maps_type_dots_and_slashes_to_config_keys(): void
    {
        config()->set('laravel-webhooks.jobs.default.foo_bar_baz', TestWebhookHandler::class);

        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'foo/bar.baz',
            'payload' => ['type' => 'foo/bar.baz'],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->process();

        Bus::assertDispatched(TestWebhookHandler::class);
    }

    /** @test */
    public function it_persists_exception_details(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test',
            'payload' => [],
            'url' => 'https://example.com/webhook',
        ]);

        $exception = WebhookFailed::typeNotHandled($webhook);

        $webhook->saveException($exception);

        $webhook->refresh();

        $this->assertSame($exception->getCode(), $webhook->exception['code']);
        $this->assertSame($exception->getMessage(), $webhook->exception['message']);
        $this->assertNotEmpty($webhook->exception['trace']);
    }

    /** @test */
    public function it_casts_payload_and_exception_to_arrays(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test',
            'payload' => ['nested' => ['key' => 'value']],
            'url' => 'https://example.com/webhook',
        ]);

        $webhook->saveException(WebhookFailed::typeNotHandled($webhook));

        $webhook->refresh();

        $this->assertIsArray($webhook->payload);
        $this->assertSame('value', $webhook->payload['nested']['key']);
        $this->assertIsArray($webhook->exception);
    }
}
