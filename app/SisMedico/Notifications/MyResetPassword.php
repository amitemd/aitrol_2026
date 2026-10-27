<?php

namespace Sis_medico\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MyResetPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = trim((string) ($notifiable->nombre1 ?? ''));
        $url = route('new-system.password.reset', [
            'email' => $notifiable->getEmailForPasswordReset(),
            'token' => $this->token,
        ]);

        return (new MailMessage)
            ->subject('Restablecimiento de contraseña de AITROL')
            ->greeting($name === '' ? 'Hola' : 'Hola '.$name)
            ->line('Recibiste este correo porque se solicitó restablecer la contraseña de tu cuenta.')
            ->action('Cambiar contraseña', $url)
            ->line('Este enlace expirará en 60 minutos. Si no realizaste esta solicitud, puedes ignorar el mensaje.')
            ->salutation('Saludos, equipo AITROL');
    }
}
