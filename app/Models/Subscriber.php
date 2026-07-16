<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use YlsIdeas\SubscribableNotifications\MailSubscriber;
use YlsIdeas\SubscribableNotifications\Contracts\CanUnsubscribe;

class Subscriber extends Model implements CanUnsubscribe
{
    use Notifiable, MailSubscriber;

    protected $fillable = [
        'name',
        'email',
        'frequency',
        'template',
        'mailing_lists',
        'subscribed_lists',
        'unsubscribed_at',
    ];

    protected $casts = [
        'mailing_lists'    => 'array',
        'subscribed_lists' => 'array',
        'unsubscribed_at'  => 'datetime',
    ];

    public function trackingLogs()
    {
        return $this->hasMany(EmailTrackingLog::class);
    }

    public function isSubscribedTo(string $list): bool
    {
        $lists = $this->subscribed_lists ?? [];
        return in_array($list, $lists);
    }
}
