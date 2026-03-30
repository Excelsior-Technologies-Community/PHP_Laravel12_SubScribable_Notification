<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Notifications\WelcomeSubscriberNotification;

class SubscriberNotificationController extends Controller
{
    /**
     * Store subscriber (simple insert)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'name' => $request->name,
            'email' => $request->email,

            // ✅ IMPORTANT (NO json_encode)
            'mailing_lists' => [
                'newsletter' => true
            ],
        ]);

        // ✅ Send Notification
        $subscriber->notify(new WelcomeSubscriberNotification('newsletter'));

        return view('success');
    }

    /**
     * Subscribe (update OR create)
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $subscriber = Subscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,

                // ✅ IMPORTANT (THIS FIXES NULL ISSUE)
                'mailing_lists' => [
                    'newsletter' => true
                ],
            ]
        );

        // ✅ Send Notification
        $subscriber->notify(new WelcomeSubscriberNotification('newsletter'));

        return view('subscribe-success', compact('subscriber'));
    }
}