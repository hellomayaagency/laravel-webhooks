<?php

namespace Hellomayaagency\LaravelWebhooks\Tests\Support;

use Hellomayaagency\LaravelWebhooks\Http\Controllers\WebhookController;

class TestWebhookController extends WebhookController
{
    protected $source = 'default';
}
