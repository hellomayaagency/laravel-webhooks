<?php

namespace Hellomayaagency\LaravelWebhooks\Models;

use Exception;
use Hellomayaagency\LaravelWebhooks\Exceptions\WebhookFailed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Data representation of a received webhook call. When receiving webhooks and
 * creating models:
 *
 * The 'source' attribute of a Webhook should be defined as the location in the
 * config for an array of 'name => value' pairs where the `name` matches the
 * `type` attribute on a webhook and where `value` is the class of a job
 * that will process the contents of that webhook.
 */
class ReceivedWebhook extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
        'exception' => 'array',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];

    /**
     * Process this webhook
     *
     * @return void
     */
    public function process(): void
    {
        $this->clearException();

        if ($this->type === '') {
            $this->saveException(WebhookFailed::missingType($this));
            return;
        }

        $job_class = $this->determineJobClass();

        if (is_null($job_class)) {
            $this->saveException(WebhookFailed::typeNotHandled($this));
            return;
        }

        if (!class_exists($job_class)) {
            $this->saveException(WebhookFailed::jobClassDoesNotExist($job_class, $this));
            return;
        }

        dispatch(new $job_class($this));
    }

    /**
     * Persist a given exception to the database as the 'most recent' exception.
     *
     * @param Exception $exception
     *
     * @return self
     */
    public function saveException(Exception $exception)
    {
        $this->exception = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $this->save();

        return $this;
    }

    /**
     * Determine the class of the handler job that should handle this webhook.
     *
     * @note - Due to the way that the config accessor interacts with the
     *         period character, these need to be replaced with underscores when
     *         naming the job types in config files!
     *
     * @return string|null
     */
    protected function determineJobClass(): ?string
    {
        $config_key = preg_replace('#[\.\/]+#', '_', $this->type);

        return Config::get(
            implode('.', [
                'jobs',
                $this->source,
                $config_key
            ]),
            null
        );
    }

    /**
     * Clear the current exception
     *
     * @return self
     */
    protected function clearException()
    {
        $this->exception = null;
        $this->save();

        return $this;
    }
}
