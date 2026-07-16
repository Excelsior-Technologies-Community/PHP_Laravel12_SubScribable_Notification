<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Notifications\WelcomeSubscriberNotification;

class SubscriberNotificationController extends Controller
{
    public const AVAILABLE_LISTS = ['newsletter', 'offers', 'updates'];

    public function subscribe(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'frequency' => 'required|in:daily,weekly,monthly',
            'lists'     => 'required|array|min:1',
            'lists.*'   => 'in:newsletter,offers,updates',
        ]);

        $subscriber = Subscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'name'             => $request->name,
                'frequency'        => $request->frequency,
                'subscribed_lists' => $request->lists,
                'mailing_lists'    => array_fill_keys($request->lists, true),
                'unsubscribed_at'  => null, // re-subscribe
            ]
        );

        // Send notification for each selected list
        foreach ($request->lists as $list) {
            $subscriber->notify(new WelcomeSubscriberNotification($list));
        }

        return view('subscribe-success', compact('subscriber'));
    }

    public function resubscribe(Request $request, Subscriber $subscriber)
    {
        $subscriber->update([
            'unsubscribed_at'  => null,
            'subscribed_lists' => self::AVAILABLE_LISTS,
            'mailing_lists'    => array_fill_keys(self::AVAILABLE_LISTS, true),
        ]);

        $subscriber->notify(new WelcomeSubscriberNotification('newsletter'));

        return view('resubscribe-success', compact('subscriber'));
    }
}
