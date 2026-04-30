<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class FailedDisbursement extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(public Loan $loan) {}

    public function envelope(): Envelope
    {
        $loan = $this->loan;

        return new Envelope(
            subject: "Failed Disbursement — (Loan #{$loan->Credit_Account_Reference})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.failed-disbursement',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
