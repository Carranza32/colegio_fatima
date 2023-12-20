<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $header;
    public $msg;
    public $email;
    public $password;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->url = env('APP_URL', null);

        $this->header = "Bienvenido a Colegio Fátima";
        $this->msg = "Su cuenta ha sido creada en Colegio Fátima. A continuación se encuentran las credenciales generadas por el sistema,
        Cambie la contraseña inmediatamente después de iniciar sesión.";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a Colegio Fátima',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.new_account',
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
