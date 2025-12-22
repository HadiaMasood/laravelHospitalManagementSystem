<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $expiringItems;
    public $expiredItems;
    public $notificationType;

    /**
     * Create a new message instance.
     */
    public function __construct($expiringItems = [], $expiredItems = [], $notificationType = 'daily')
    {
        $this->expiringItems = $expiringItems;
        $this->expiredItems = $expiredItems;
        $this->notificationType = $notificationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Medicine Expiry Alert - ' . ucfirst($this->notificationType) . ' Report';
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.expiry-notification',
            with: [
                'expiringItems' => $this->expiringItems,
                'expiredItems' => $this->expiredItems,
                'notificationType' => $this->notificationType,
            ],
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