<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

use Exception;
use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;
use Hellomayaagency\LaravelWebhooks\Tests\Support\FailingWebhookController;
use Hellomayaagency\LaravelWebhooks\Tests\Support\TestWebhookController;
use Hellomayaagency\LaravelWebhooks\Tests\Support\TestWebhookHandler;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;

class WebhookControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('laravel-webhooks.jobs.default.test_event', TestWebhookHandler::class);

        Route::post('/webhooks/test', TestWebhookController::class);
        Route::post('/webhooks/failing', FailingWebhookController::class);
    }

    /** @test */
    public function it_stores_and_processes_incoming_webhook(): void
    {
        $response = $this->postJson('/webhooks/test', [
            'type' => 'test.event',
            'data' => 'payload-value',
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'ok']);

        $this->assertDatabaseCount('received_webhooks', 1);

        $webhook = ReceivedWebhook::first();

        $this->assertSame('default', $webhook->source);
        $this->assertSame('test.event', $webhook->type);
        $this->assertSame('payload-value', $webhook->payload['data']);
        $this->assertStringContainsString('/webhooks/test', $webhook->url);

        Bus::assertDispatched(TestWebhookHandler::class);
    }

    /** @test */
    public function it_saves_exception_and_rethrows_when_processing_fails(): void
    {
        $this->withoutExceptionHandling();

        try {
            $this->postJson('/webhooks/failing', ['type' => 'test.event']);
            $this->fail('Expected exception was not thrown.');
        } catch (Exception $exception) {
            $this->assertSame('Processing failed', $exception->getMessage());
        }

        $webhook = ReceivedWebhook::first();

        $this->assertNotNull($webhook->exception);
        $this->assertSame('Processing failed', $webhook->exception['message']);
    }
}
