<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'              => User::count(),
            'orders'             => Order::count(),
            'containers'         => Container::count(),
            'revenue'            => Payment::where('status', 'paid')->sum('amount'),
            'pending_payments'   => Payment::where('status', 'pending')->count(),
            'running_containers' => Container::where('status', 'running')->count(),
        ];

        $recentOrders = Order::with(['user', 'package'])->latest()->take(10)->get();
        $recentPayments = Payment::with(['user', 'order'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentPayments'));
    }
}
