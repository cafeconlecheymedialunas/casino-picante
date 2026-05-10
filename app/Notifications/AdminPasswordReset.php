<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminPasswordReset extends Notification
{
    use Queueable;

    public function __construct(public string $resetUrl) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Restablecer contraseña - Panel RED PICANTES')
            ->line('Se solicitó un restablecimiento de contraseña.')
            ->action('Restablecer contraseña', $this->resetUrl)
            ->line('Este enlace caduca en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignorá este email.');
    }
}
