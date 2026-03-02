<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityCategory;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where(static function($q) use ($request): void {
                $q->where('event', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->paginate(50)->withQueryString();
        $categories = ActivityCategory::cases();

        return view('admin.activity-logs.index', compact('logs', 'categories'));
    }
}
