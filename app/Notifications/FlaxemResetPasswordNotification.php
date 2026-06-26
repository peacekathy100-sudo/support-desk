<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlaxemResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset your Flaxem Support Desk password')
            ->view('emails.auth.password-reset', [
                'token' => $this->token,
                'user' => $notifiable,
                'resetUrl' => url(route('password.reset', ['token' => $this->token], false)),
            ]);
    }
}
