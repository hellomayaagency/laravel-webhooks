<?php

namespace Hellomayaagency\LaravelWebhooks\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * Basis for Webhook processing jobs
 */
abstract class WebhookHandler implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Webhook to process
     *
     * @var Webhook
     */
    protected $webhook;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    abstract public function handle();

    /**
     * Gets all or part of the payload of the webhook.
     *
     * @param string|null $fragment
     *
     * @return mixed
     */
    protected function payload(string $fragment = null)
    {
        if (is_null($fragment)) {
            return $this->webhook->payload;
        }

        return Arr::get($this->webhook->payload, $fragment);
    }
}
