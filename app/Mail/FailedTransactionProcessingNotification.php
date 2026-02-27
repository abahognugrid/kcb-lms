<?php

namespace App\Mail;

use App\Models\Partner;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FailedTransactionProcessingNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Partner $partner;

    public Collection $transactions;

    /**
     * Create a new message instance.
     */
    public function __construct(Partner $partner, array $transactionIds)
    {
        $this->partner = $partner;
        $this->transactions = Transaction::find($transactionIds);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Unprocessed Transaction Alert - {$this->partner->Institution_Name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.failed-transaction-processing',
            with: [
                'partner' => $this->partner,
                'transactions' => $this->transactions,
                'transactionCount' => count($this->transactions),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
