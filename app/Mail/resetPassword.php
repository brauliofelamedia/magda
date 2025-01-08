<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class resetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $user, private $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reinicio de contraseña',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->password,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
