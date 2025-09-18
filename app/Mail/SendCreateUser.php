<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MailerSend\LaravelDriver\MailerSendTrait;
use Illuminate\Support\Arr;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Variable;

class SendCreateUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    public function __construct( $user,$password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Datos de acceso para el usuario - '.$this->user->name,
        );
    }

    public function content()
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
