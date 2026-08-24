<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $details,
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
        public ?string $attachmentMime = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->details['email'], $this->details['name']),
            ],
            subject: 'Visitation request for '.$this->details['intended_date'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visitation-request',
            text: 'emails.visitation-request-text',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->attachmentPath === null || $this->attachmentName === null) {
            return [];
        }

        $attachment = Attachment::fromPath($this->attachmentPath)
            ->as($this->attachmentName);

        if ($this->attachmentMime !== null) {
            $attachment->withMime($this->attachmentMime);
        }

        return [$attachment];
    }
}
