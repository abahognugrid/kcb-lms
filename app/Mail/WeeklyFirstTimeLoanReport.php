<?php

namespace App\Mail;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyFirstTimeLoanReport extends Mailable
{
    use Queueable, SerializesModels;

    public Partner $partner;

    public array $analysis;

    /**
     * Create a new message instance.
     */
    public function __construct(Partner $partner, array $analysis)
    {
        $this->partner = $partner;
        $this->analysis = $analysis;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly First-Time Loan Report Alert - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'reports.mails.weekly-first-time-loan-report',
            with: [
                'partner' => $this->partner,
                'analysis' => $this->analysis,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
