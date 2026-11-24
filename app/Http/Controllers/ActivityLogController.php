<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityFilterRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(ActivityFilterRequest $request)
    {
        $filters = $request->validated();

        $logs = ActivityLog::where('user_id', $request->user()->id)
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('activity.index', compact('logs', 'filters'));
    }
}
