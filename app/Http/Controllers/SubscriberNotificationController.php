<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Notifications\WelcomeSubscriberNotification;

class SubscriberNotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:subscribers,email',
            'frequency'=>'required'
        ]);

        $subscriber=Subscriber::create([

            'name'=>$request->name,
            'email'=>$request->email,

            'frequency'=>$request->frequency,

            'mailing_lists'=>[
                'newsletter'=>true
            ]

        ]);

        $subscriber->notify(
            new WelcomeSubscriberNotification('newsletter')
        );

        return view('success');
    }


    public function subscribe(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'frequency'=>'required'
        ]);

        $subscriber=Subscriber::updateOrCreate(

            ['email'=>$request->email],

            [

                'name'=>$request->name,

                'frequency'=>$request->frequency,

                'mailing_lists'=>[
                    'newsletter'=>true
                ]
            ]
        );

        $subscriber->notify(
            new WelcomeSubscriberNotification('newsletter')
        );

        return view(
            'subscribe-success',
            compact('subscriber')
        );
    }
}