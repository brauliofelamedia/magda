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
    use Queueable, SerializesModels, MailerSendTrait;

    public function __construct(private $user, private $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Datos de acceso para el usuario - '.$this->user->name,
        );
    }

    public function content()
    {
        // Additional options for MailerSend API features
        $this->mailersend(
            template_id: 'ynrw7gyxvynl2k8e',
            personalization: [
                new Personalization($this->user->email, [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'institution' => $this->user->name_institution,
                    'password' => $this->password,
                ])
            ],
            precedenceBulkHeader: true,
        );

        return new Content(
            view: 'emails.create',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
