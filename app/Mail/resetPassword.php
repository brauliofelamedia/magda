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

    public function __construct(private $user, private $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reinicio de contraseña',
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
                    'password' => $this->password,
                ])
            ],
            precedenceBulkHeader: true,
        );

        return new Content(
            view: 'emails.reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
