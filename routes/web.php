<?php

use Illuminate\Support\Facades\Route;
use App\Models\Subscriber;
use App\Http\Controllers\SubscriberNotificationController;

// Show subscribe form
Route::get('/', function () {
    return view('subscribe');
});

// Handle subscribe
Route::post('/subscribe', [SubscriberNotificationController::class, 'subscribe'])
    ->name('subscribe');

// Unsubscribe
Route::get('/unsubscribe/{subscriber}', function (Subscriber $subscriber) {
    $subscriber->unsubscribed_at = now();
    $subscriber->save();

    return view('unsubscribe-success');
})->name('unsubscribe');