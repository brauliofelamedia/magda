<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendCreateUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $user, private $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Datos de acceso para el usuario - '.$this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.create',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'institution' => $this->user->name_institution,
                'password' => $this->password,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
