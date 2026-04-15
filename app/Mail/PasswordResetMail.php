<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enlace;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($enlace, $email)
    {
        $this->enlace = $enlace;
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Restablecer tu contraseña - Deuditas')
                    ->view('emails.password-reset')
                    ->with([
                        'enlace' => $this->enlace,
                        'email' => $this->email
                    ]);
    }
}