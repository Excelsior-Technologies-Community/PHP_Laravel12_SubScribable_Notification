<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $subscribers = Subscriber::query()
            ->when($search, function ($query) use ($search) {

                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");

            })
            ->orderBy('id', 'asc')
            ->paginate(3)
            ->withQueryString();

        $totalSubscribers = Subscriber::count();

        return view(
            'admin.subscribers',
            compact(
                'subscribers',
                'totalSubscribers'
            )
        );
    }
}