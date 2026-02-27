<?php

namespace App\Exceptions;

use App\Notifications\InternalAccountingExceptionNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

abstract class InternalAccountingException extends Exception
{
    protected $details = null;

    public const GENERIC_MESSAGE = 'Transaction failed to save. Transaction details have been sent to our team.';

    public function __construct(string $message, array $details)
    {
        $this->message = $message;

        parent::__construct($this->message);

        $this->details = [
            'details' => $details,
        ];
    }

    public function genericMessage()
    {
        return self::GENERIC_MESSAGE;
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report()
    {
        if (config('app.env') === 'testing') {
            return;
        }

        $webhook_url = config('app.slack_webhook_url_accounting_exception_detected');

        if (!$webhook_url) {
            return;
        }

        $details = $this->getDetails();

        Notification::route('slack', $webhook_url)
            ->route('title', $this->message)
            ->route('details', $details)
            ->notify(new InternalAccountingExceptionNotification());
    }
}
