<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Rendszerértesítések küldésére szolgáló levél osztály.
 */
class SystemNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Új üzenet példány létrehozása.
     *
     * @param string $title Az értesítés címe (e-mail tárgya)
     * @param string $message Az értesítés szöveges tartalma
     * @param string|null $buttonUrl Opcionális gomb URL címe
     * @param string|null $buttonText Opcionális gomb felirata
     */
    public function __construct(
        public string $title,
        public string $message,
        public ?string $buttonUrl = null,
        public ?string $buttonText = null
    ) {}

    /**
     * Az üzenet borítékának (envelope) meghatározása.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title, // Az e-mail tárgya a megadott cím lesz
        );
    }

    /**
     * Az üzenet tartalmának meghatározása.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.system-notification', // Markdown sablon használata
        );
    }

    /**
     * Az üzenethez tartozó mellékletek lekérése.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
