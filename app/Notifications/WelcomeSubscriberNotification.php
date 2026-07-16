<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTrackingLog;
use Illuminate\Support\Str;

class WelcomeSubscriberNotification extends Notification
{
    public string $mailingList;
    public string $token;

    public function __construct(string $mailingList = 'newsletter')
    {
        $this->mailingList = $mailingList;
        $this->token = Str::uuid()->toString();
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Create tracking log
        EmailTrackingLog::create([
            'subscriber_id' => $notifiable->id,
            'token'         => $this->token,
            'mailing_list'  => $this->mailingList,
            'subject'       => 'Subscription Successful - ' . ucfirst($this->mailingList),
            'delivered_at'  => now(),
        ]);

        $openPixel    = url('/track/open/' . $this->token);
        $clickUrl     = url('/track/click/' . $this->token . '?url=' . urlencode(url('/')));
        $unsubUrl     = url('/unsubscribe/' . $notifiable->id);

        return (new MailMessage)
            ->subject('Subscription Successful - ' . ucfirst($this->mailingList))
            ->greeting("Hello {$notifiable->name}!")
            ->line('Thank you for subscribing to **' . ucfirst($this->mailingList) . '**!')
            ->line('You will receive ' . $notifiable->frequency . ' updates.')
            ->action('Visit Our Site', $clickUrl)
            ->line('To unsubscribe: [Click here](' . $unsubUrl . ')')
            ->line('<img src="' . $openPixel . '" width="1" height="1" style="display:none">');
    }
}
