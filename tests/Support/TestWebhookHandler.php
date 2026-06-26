<?php

namespace Hellomayaagency\LaravelWebhooks\Tests\Support;

use Hellomayaagency\LaravelWebhooks\Jobs\WebhookHandler;
use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;

class TestWebhookHandler extends WebhookHandler
{
    public static ?ReceivedWebhook $handledWebhook = null;

    public function getWebhook(): ReceivedWebhook
    {
        return $this->webhook;
    }

    public function handle(): void
    {
        self::$handledWebhook = $this->webhook;
    }
}
