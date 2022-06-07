<?php

namespace Hellomayaagency\LaravelWebhooks\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Response;

/**
 * `Borrowed` from spatie/laravel-stripe-webhooks as it doesn't support down to Laravel 5.4
 */

abstract class WebhookController extends Controller
{
    /**
     * Source to assign to received webhooks handled by this controller.
     */
    protected $source = 'default';

    public function __invoke(Request $request)
    {
        $model = config('laravel-webhooks.model');

        $payload = $this->getPayload();
        $url = $this->getRecipientUrl();

        $received_webhook = $model::create([
            'source' => $this->source,
            'type' =>  $this->getType($payload, $url),
            'payload' => $payload,
            'url' => $url,
        ]);

        try {
            $received_webhook->process();
        } catch (Exception $exception) {
            $received_webhook->saveException($exception);

            throw $exception;
        }

        return $this->getSuccessResponse();
    }

    /**
     * Determine the webhook's payload to save to the model. Should be a an array
     *
     * @return array
     */
    protected function getPayload(): array
    {
        return RequestFacade::input() ?? [];
    }

    /**
     * Determine the webhook's recipient url to save to the model
     *
     * @return string
     */
    protected function getRecipientUrl(): string
    {
        return RequestFacade::fullUrl();
    }

    /**
     * A success response for this controller
     *
     * @return JsonResponse
     */
    protected function getSuccessResponse(): JsonResponse
    {
        return Response::json([
            'message' => 'ok'
        ], 200);
    }

    /**
     * Determine the job type base on the webhook's payload or recipient url to
     * save to the model
     *
     * @param array  $payload
     * @param string $url
     *
     * @return string
     */
    protected function getType(array $payload, string $url): string
    {
        return $payload['type'] ?? '';
    }
}
