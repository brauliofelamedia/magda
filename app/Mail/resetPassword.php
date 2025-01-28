<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class resetPassword extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reinicio de contraseña',
        );
    }

    public function content()
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
}

