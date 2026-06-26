<?php

namespace Hellomayaagency\LaravelWebhooks\Tests;

use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;
use Hellomayaagency\LaravelWebhooks\Tests\Support\TestWebhookHandler;

class WebhookHandlerTest extends TestCase
{
    /** @test */
    public function it_exposes_full_payload_when_no_fragment_is_given(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test',
            'payload' => ['user' => ['id' => 42]],
            'url' => 'https://example.com/webhook',
        ]);

        $handler = new class($webhook) extends TestWebhookHandler
        {
            public ?array $fullPayload = null;

            public function handle(): void
            {
                $this->fullPayload = $this->payload();
            }
        };

        $handler->handle();

        $this->assertSame(['user' => ['id' => 42]], $handler->fullPayload);
    }

    /** @test */
    public function it_exposes_payload_fragment_when_given(): void
    {
        $webhook = ReceivedWebhook::create([
            'source' => 'default',
            'type' => 'test',
            'payload' => ['user' => ['id' => 42, 'name' => 'Ada']],
            'url' => 'https://example.com/webhook',
        ]);

        $handler = new class($webhook) extends TestWebhookHandler
        {
            public mixed $userId = null;

            public mixed $userName = null;

            public function handle(): void
            {
                $this->userId = $this->payload('user.id');
                $this->userName = $this->payload('user.name');
            }
        };

        $handler->handle();

        $this->assertSame(42, $handler->userId);
        $this->assertSame('Ada', $handler->userName);
    }
}
