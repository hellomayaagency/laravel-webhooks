<?php

namespace Hellomayaagency\LaravelWebhooks\Tests\Support;

use Exception;
use Hellomayaagency\LaravelWebhooks\Http\Controllers\WebhookController;
use Illuminate\Http\Request;

class FailingWebhookController extends WebhookController
{
    public function __invoke(Request $request)
    {
        $model = config('laravel-webhooks.model');

        $receivedWebhook = $model::create([
            'source' => $this->source,
            'type' => 'test.event',
            'payload' => $request->all(),
            'url' => $request->fullUrl(),
        ]);

        try {
            throw new Exception('Processing failed');
        } catch (Exception $exception) {
            $receivedWebhook->saveException($exception);

            throw $exception;
        }
    }
}
