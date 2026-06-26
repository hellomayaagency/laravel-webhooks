<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

use Hellomayaagency\LaravelWebhooks\Exceptions\WebhookFailed;
use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;

class WebhookFailedTest extends TestCase
{
    /** @test */
    public function it_renders_error_response(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test',
            'payload' => [],
            'url' => 'https://example.com/webhook',
        ]);

        $exception = WebhookFailed::typeNotHandled($webhook);

        $response = $exception->render(request());

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(
            ['error' => $exception->getMessage()],
            json_decode($response->getContent(), true)
        );
    }

    /** @test */
    public function it_builds_descriptive_exception_messages(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'invoice.paid',
            'payload' => [],
            'url' => 'https://example.com/webhook',
        ]);

        $this->assertStringContainsString((string) $webhook->id, WebhookFailed::missingType($webhook)->getMessage());
        $this->assertStringContainsString('invoice.paid', WebhookFailed::typeNotHandled($webhook)->getMessage());
        $this->assertStringContainsString(
            'GhostHandler',
            WebhookFailed::jobClassDoesNotExist('\\App\\Jobs\\GhostHandler', $webhook)->getMessage()
        );
    }
}
