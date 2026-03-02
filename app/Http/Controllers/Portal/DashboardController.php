<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'containers'     => $user->containers()->count(),
            'active'         => $user->containers()->where('status', 'running')->count(),
            'orders'         => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
        ];

        $recentContainers = $user->containers()->latest()->take(5)->get();
        $recentOrders = $user->orders()->with('package')->latest()->take(5)->get();

        return view('portal.dashboard', compact('stats', 'recentContainers', 'recentOrders'));
    }
}
