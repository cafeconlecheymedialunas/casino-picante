<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPasswordReset extends Notification
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
            ->subject('Restablecer contrasena - RED PICANTES')
            ->line('Se solicito un restablecimiento de contrasena para tu cuenta de cliente.')
            ->action('Restablecer contrasena', $this->resetUrl)
            ->line('Este enlace caduca en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este email.');
    }
}
