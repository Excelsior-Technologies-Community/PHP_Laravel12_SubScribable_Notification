<?php

use Illuminate\Support\Facades\Route;
use App\Models\Subscriber;
use App\Http\Controllers\SubscriberNotificationController;
use App\Http\Controllers\SubscriberAdminController;

// Subscribe form
Route::get('/', fn() => view('subscribe'));

// Subscribe (multiple lists)
Route::post('/subscribe', [SubscriberNotificationController::class, 'subscribe'])->name('subscribe');

// Unsubscribe
Route::get('/unsubscribe/{subscriber}', function (Subscriber $subscriber) {
    $subscriber->update(['unsubscribed_at' => now()]);
    return view('unsubscribe-success', compact('subscriber'));
})->name('unsubscribe');

// Re-subscribe
Route::get('/resubscribe/{subscriber}', [SubscriberNotificationController::class, 'resubscribe'])->name('resubscribe');

// Email Tracking
Route::get('/track/open/{token}',  [SubscriberAdminController::class, 'trackOpen'])->name('track.open');
Route::get('/track/click/{token}', [SubscriberAdminController::class, 'trackClick'])->name('track.click');

// Admin Dashboard
Route::get('/admin/subscribers',     [SubscriberAdminController::class, 'index'])->name('admin.subscribers');
Route::get('/admin/analytics',       [SubscriberAdminController::class, 'analytics'])->name('admin.analytics');
Route::get('/admin/template-builder',  [SubscriberAdminController::class, 'templateBuilder'])->name('admin.template-builder');
Route::post('/admin/template-builder', [SubscriberAdminController::class, 'templateBuilder'])->name('admin.template-builder.preview');
