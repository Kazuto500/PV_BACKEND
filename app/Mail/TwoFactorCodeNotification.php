<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code = $this->code;

        // Construye el contenido del correo electrónico directamente
        $content = "Tu código de autenticación en dos pasos es: $code\n";
        $content .= "Este código expirará en 10 minutos.\n";
        $content .= "Si no intentaste iniciar sesión con autenticación en dos pasos, ignora este correo.\n";

        return $this
            ->subject('Two Factor Code Notification')
            ->text(function ($message) use ($content) {
                $message->line($content);
                $message->action('Iniciar sesión', config('app.url'));
                $message->line('Gracias por usar nuestra aplicación.');
            });
    }
}
