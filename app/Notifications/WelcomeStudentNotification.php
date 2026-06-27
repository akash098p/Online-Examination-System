<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeStudentNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Academix')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for registering as a student on Academix.')
            ->line('Your account has been created successfully and you can now access your dashboard.')
            ->action('Go to your dashboard', url(route('dashboard', absolute: false)))
            ->line('If you did not register for this account, please contact support immediately.');
    }
}
