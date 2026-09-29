<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        // Hanya DMO dan Admin yang bisa melihat audit log
        $this->middleware('role:dmo,admin');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'module', 'action', 'user_id', 'date_from', 'date_to']);

        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('record_id', 'like', "%{$s}%")
                  ->orWhere('module', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Dropdown options
        $modules = ActivityLog::distinct()->orderBy('module')->pluck('module')->filter();
        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action')->filter();
        $users   = User::where('status', 'active')->orderBy('name')->get(['id', 'name', 'role']);

        // Stats for today
        $todayCount = ActivityLog::whereDate('created_at', today())->count();
        $totalCount = ActivityLog::count();

        return view('audit_log.index', compact(
            'logs', 'filters', 'modules', 'actions', 'users',
            'todayCount', 'totalCount'
        ));
    }
}
