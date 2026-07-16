<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTrackingLog extends Model
{
    protected $fillable = [
        'subscriber_id',
        'token',
        'mailing_list',
        'subject',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'click_url',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'opened_at'    => 'datetime',
        'clicked_at'   => 'datetime',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
