<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiringStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $stocks;
    public $daysUntil;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($stocks, $daysUntil = 30)
    {
        $this->stocks = $stocks;
        $this->daysUntil = $daysUntil;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: '⚠️ Medicines Expiring Soon - Action Required',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.expiring-stock-alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments()
    {
        return [];
    }
}