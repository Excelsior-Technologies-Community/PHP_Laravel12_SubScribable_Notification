<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use YlsIdeas\SubscribableNotifications\Facades\Subscriber;
use Illuminate\Support\Facades\Route;
use App\Models\Subscriber as SubscriberModel;

class SubscriberServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Unsubscribe from specific mailing list
        Route::get('/unsubscribe/{subscriber}/{mailingList}', function ($id, $mailingList) {
            $subscriber = SubscriberModel::findOrFail($id);
            $subscriber->mailing_lists = array_merge($subscriber->mailing_lists ?? [], [
                $mailingList => false,
            ]);
            $subscriber->save();

            return view('unsubscribe.confirmed', [
                'message' => "You unsubscribed from {$mailingList}"
            ]);
        })->name('unsubscribe.list');

        // Unsubscribe from all emails
        Route::get('/unsubscribe-all/{subscriber}', function ($id) {
            $subscriber = SubscriberModel::findOrFail($id);
            $subscriber->unsubscribed_at = now();
            $subscriber->save();

            return view('unsubscribe.confirmed', [
                'message' => "You unsubscribed from all emails"
            ]);
        })->name('unsubscribe.all');

        // Optional callbacks
        Subscriber::onUnsubscribeFromMailingList(function ($subscriber, $mailingList) {
            $subscriber->mailing_lists = array_merge($subscriber->mailing_lists ?? [], [
                $mailingList => false,
            ]);
            $subscriber->save();
        });

        Subscriber::onUnsubscribeFromAllMailingLists(function ($subscriber) {
            $subscriber->unsubscribed_at = now();
            $subscriber->save();
        });

        Subscriber::onCompletion(function ($subscriber, $mailingList = null) {
            return view('unsubscribe.confirmed', [
                'message' => $mailingList
                    ? "You unsubscribed from {$mailingList}"
                    : "You unsubscribed from all emails"
            ]);
        });
    }

    public function register(): void
    {
        //
    }
}