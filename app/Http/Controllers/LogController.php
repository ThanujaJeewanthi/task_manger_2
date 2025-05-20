<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    /**
     * Display a listing of the logs with optional filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Log::with(['user', 'userRole']);

        // Apply filters
        if ($request->filled('username')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->username . '%')
                  ->orWhere('name', 'like', '%' . $request->username . '%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Default sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $logs = $query->paginate(15);
        $users = User::where('active', true)->get();
        $roles = UserRole::where('active', true)->get();

        // unique action types for filtering
        $actionTypes = Log::select('action')->distinct()->pluck('action');

        return view('logs.index', compact('logs', 'users', 'roles', 'actionTypes'));
    }

    /**
     * Display the specified log.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $log = Log::with(['user', 'userRole'])->findOrFail($id);
        return view('logs.show', compact('log'));
    }

    /**
     * Clear logs with optional filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(Request $request)
    {
        $query = Log::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $count = $query->count();

        // Create a log record for this action
        Log::create([
            'action' => 'clear_logs',
            'user_id' => Auth::id(),
            'user_role_id' => Auth::user()->user_role_id ?? null,
            'ip_address' => $request->ip(),
            'description' => "Cleared {$count} logs with filters: " . json_encode($request->only(['date_from', 'date_to', 'action', 'user_id'])),
            'active' => true,
        ]);

        // Update logs to inactive instead of deleting them
        $query->update(['active' => false]);

        return redirect()->route('admin.logs.index')
            ->with('success', "{$count} logs have been cleared successfully.");
    }
}
