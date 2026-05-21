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
        'mailing_lists',
        'unsubscribed_at'
    ];

    protected $casts = [
        'mailing_lists' => 'array',
        'unsubscribed_at' => 'datetime',
    ];
}