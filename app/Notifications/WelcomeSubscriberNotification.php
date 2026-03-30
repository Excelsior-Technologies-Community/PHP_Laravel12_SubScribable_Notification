<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeSubscriberNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Subscription Successful')
            ->greeting("Hello {$notifiable->name}")
            ->line('Thank you for subscribing!')
            ->line(' ')
            ->line('If you wish to unsubscribe, click below:')
            ->action('Unsubscribe', url('/unsubscribe/' . $notifiable->id));
    }
}