<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TempPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tempPassword;
    public $loginUrl;
    public $userEmail;

    public function __construct(string $tempPassword, string $userEmail)
    {
        $this->tempPassword = $tempPassword;
        $this->userEmail = $userEmail;
        $this->loginUrl = route('login'); // Or your custom login route
    }

    public function build()
    {
        return $this->subject('Your Temporary Password')
            ->markdown('mail.temp-password') // Using markdown for better styling
            ->with([
                'tempPassword' => $this->tempPassword,
                'loginUrl' => $this->loginUrl,
                'userEmail' => $this->userEmail
            ]);
    }
}
