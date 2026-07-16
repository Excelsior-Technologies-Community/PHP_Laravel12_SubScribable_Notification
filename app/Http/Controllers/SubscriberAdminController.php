<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\EmailTrackingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriberAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $subscribers = Subscriber::query()
            ->when($search, fn($q) => $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $total        = Subscriber::count();
        $active       = Subscriber::whereNull('unsubscribed_at')->count();
        $unsubscribed = Subscriber::whereNotNull('unsubscribed_at')->count();

        $listCounts = [
            'newsletter' => Subscriber::whereNull('unsubscribed_at')
                ->whereJsonContains('subscribed_lists', 'newsletter')->count(),
            'offers'     => Subscriber::whereNull('unsubscribed_at')
                ->whereJsonContains('subscribed_lists', 'offers')->count(),
            'updates'    => Subscriber::whereNull('unsubscribed_at')
                ->whereJsonContains('subscribed_lists', 'updates')->count(),
        ];

        // Last 7 days new subscribers
        $dailyStats = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date'  => $date->format('M d'),
                'count' => Subscriber::whereDate('created_at', $date->toDateString())->count(),
            ];
        });

        return view('admin.subscribers', compact(
            'subscribers', 'total', 'active', 'unsubscribed', 'listCounts', 'dailyStats'
        ));
    }

    public function analytics(Request $request)
    {
        $logs = EmailTrackingLog::with('subscriber')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalDelivered = EmailTrackingLog::whereNotNull('delivered_at')->count();
        $totalOpened    = EmailTrackingLog::whereNotNull('opened_at')->count();
        $totalClicked   = EmailTrackingLog::whereNotNull('clicked_at')->count();
        $totalSent      = EmailTrackingLog::count();

        $openRate  = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0;
        $deliveryRate = $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0;

        // Per mailing list breakdown
        $listAnalytics = collect(['newsletter', 'offers', 'updates'])->map(function ($list) {
            $sent      = EmailTrackingLog::where('mailing_list', $list)->count();
            $opened    = EmailTrackingLog::where('mailing_list', $list)->whereNotNull('opened_at')->count();
            $clicked   = EmailTrackingLog::where('mailing_list', $list)->whereNotNull('clicked_at')->count();
            return [
                'list'       => $list,
                'sent'       => $sent,
                'opened'     => $opened,
                'clicked'    => $clicked,
                'open_rate'  => $sent > 0 ? round(($opened / $sent) * 100, 1) : 0,
                'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 1) : 0,
            ];
        });

        return view('admin.analytics', compact(
            'logs', 'totalDelivered', 'totalOpened', 'totalClicked',
            'totalSent', 'openRate', 'clickRate', 'deliveryRate', 'listAnalytics'
        ));
    }

    public function templateBuilder(Request $request)
    {
        $templates = [
            'default'   => ['name' => 'Default Clean', 'bg' => '#ffffff', 'accent' => '#22c55e'],
            'dark'      => ['name' => 'Dark Mode',     'bg' => '#1e293b', 'accent' => '#6366f1'],
            'corporate' => ['name' => 'Corporate',     'bg' => '#f8fafc', 'accent' => '#0ea5e9'],
            'promo'     => ['name' => 'Promo/Offers',  'bg' => '#fff7ed', 'accent' => '#f97316'],
        ];

        $preview = null;
        if ($request->isMethod('post')) {
            $preview = [
                'subject'  => $request->subject ?? 'Your Newsletter',
                'greeting' => $request->greeting ?? 'Hello Subscriber!',
                'body'     => $request->body ?? 'Your email content here.',
                'cta'      => $request->cta ?? 'Visit Now',
                'cta_url'  => $request->cta_url ?? url('/'),
                'template' => $request->template ?? 'default',
                'style'    => $templates[$request->template ?? 'default'],
            ];
        }

        return view('admin.template-builder', compact('templates', 'preview'));
    }

    // Tracking: open pixel
    public function trackOpen(string $token)
    {
        EmailTrackingLog::where('token', $token)
            ->whereNull('opened_at')
            ->update(['opened_at' => now()]);

        // Return 1x1 transparent GIF
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache');
    }

    // Tracking: click redirect
    public function trackClick(Request $request, string $token)
    {
        $log = EmailTrackingLog::where('token', $token)->first();
        if ($log) {
            $log->update([
                'clicked_at' => $log->clicked_at ?? now(),
                'click_url'  => $request->url ?? url('/'),
            ]);
        }
        return redirect($request->query('url', url('/')));
    }
}
