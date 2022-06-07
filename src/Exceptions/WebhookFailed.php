<?php

namespace Hellomayaagency\LaravelWebhooks\Exceptions;

use Exception;
use Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook;

class WebhookFailed extends Exception
{
    public static function jobClassDoesNotExist(string $job_class, ReceivedWebhook $webhook)
    {
        return new static("Could not process webhook id `{$webhook->id}` of type `{$webhook->type} because the configured jobclass `$job_class` does not exist.");
    }

    public static function typeNotHandled(ReceivedWebhook $webhook)
    {
        return new static("Did not process webhook id `{$webhook->id}` of type `{$webhook->type} because there is no configured jobclass.");
    }

    public static function missingType(ReceivedWebhook $webhook)
    {
        return new static("Webhook call id `{$webhook->id}` did not contain a type. Valid Stripe webhook calls should always contain a type.");
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }
}
