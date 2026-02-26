<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Ütemezés létrehozásáról szóló értesítő levél.
 */
class SchedulingCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Új üzenet példány létrehozása.
     */
    public function __construct()
    {
        //
    }

    /**
     * Az üzenet borítékának (envelope) meghatározása.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ütemezés létrehozva', // Az e-mail tárgya
        );
    }

    /**
     * Az üzenet tartalmának meghatározása.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.scheduling-created', // Levél sablon elérési útja
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
