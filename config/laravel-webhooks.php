<?php

return [

    /**
     * Model used to represent a received webhook.
     */
    'model' => \Hellomayaagency\LaravelWebhooks\Models\ReceivedWebhook::class,

    /**
     * Map of webhook source, types and job classes.
     */
    'jobs' => [

        // example handler
        'default' => [
            'webhook_name' => '\\App\\Jobs\\HandlerClass',
        ],

    ],

];
