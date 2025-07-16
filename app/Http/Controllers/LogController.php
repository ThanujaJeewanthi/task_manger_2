<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Job;
use App\Models\JobActivityLog;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Client;
use App\Models\Company;  // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class LogController extends Controller
{
    /**
     * Display a listing of the logs with enhanced project filtering.
     */
    public function index(Request $request)
    {
        // Determine if this is project logs or system logs
        $view_type = $request->get('view', 'project'); // 'project' or 'system'
        
        if ($view_type === 'system') {
            return $this->systemLogs($request);
        }
        
        return $this->projectLogs($request);
    }

    /**
     * Display project logs with job-related filtering
     */
    public function projectLogs(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Base query for job activity logs
        $query = JobActivityLog::with(['job.jobType', 'job.client', 'job.equipment', 'user', 'affectedUser'])
            ->whereHas('job', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        // Apply date filters (default to today)
        $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Filter by specific job
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        // Filter by equipment
        if ($request->filled('equipment_id')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('equipment_id', $request->equipment_id);
            });
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // Filter by employee (user who performed action or was affected)
        if ($request->filled('employee_id')) {
            $employeeUserId = Employee::find($request->employee_id)?->user_id;
            if ($employeeUserId) {
                $query->where(function($q) use ($employeeUserId) {
                    $q->where('user_id', $employeeUserId)
                      ->orWhere('affected_user_id', $employeeUserId);
                });
            }
        }

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by activity category
        if ($request->filled('activity_category')) {
            $query->where('activity_category', $request->activity_category);
        }

        // Filter by job status
        if ($request->filled('job_status')) {
            $query->whereHas('job', function($q) use ($request) {
                $q->where('status', $request->job_status);
            });
        }

        // Filter by priority level
        if ($request->filled('priority_level')) {
            $query->where('priority_level', $request->priority_level);
        }

        // Only show major activities if requested
        if ($request->filled('major_only') && $request->major_only == '1') {
            $query->where('is_major_activity', true);
        }

        // Search in descriptions
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('activity_type', 'like', "%{$searchTerm}%")
                  ->orWhereHas('job', function($subQ) use ($searchTerm) {
                      $subQ->where('description', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $logs = $query->paginate(20);

        // Get filter options for dropdowns
        $jobs = Job::where('company_id', $companyId)
            ->where('active', true)
            ->with(['jobType', 'client', 'equipment'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
            
        $equipment = Equipment::where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('name')
            ->get();
            
        $clients = Client::where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('name')
            ->get();
            
        $employees = Employee::where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        // Get distinct values for filters
        $activityTypes = JobActivityLog::whereHas('job', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->select('activity_type')
            ->distinct()
            ->pluck('activity_type');

        $activityCategories = JobActivityLog::whereHas('job', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->select('activity_category')
            ->distinct()
            ->pluck('activity_category');

        // Get summary statistics for the filtered period
        $stats = $this->getProjectLogStats($dateFrom, $dateTo, $companyId);

        return view('logs.project', compact(
            'logs', 'jobs', 'equipment', 'clients', 'employees',
            'activityTypes', 'activityCategories', 'stats', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Display system logs (original functionality)
     */
    public function systemLogs(Request $request)
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

        return view('logs.system', compact('logs', 'users', 'roles', 'actionTypes'));
    }

    /**
     * Get project log statistics
     */
    private function getProjectLogStats($dateFrom, $dateTo, $companyId)
    {
        $baseQuery = JobActivityLog::whereHas('job', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total_activities' => $baseQuery->count(),
            'jobs_with_activity' => $baseQuery->distinct('job_id')->count('job_id'),
            'jobs_created' => (clone $baseQuery)->where('activity_type', 'created')->count(),
            'jobs_completed' => (clone $baseQuery)->where('activity_type', 'completed')->count(),
            'pending_jobs' => Job::where('company_id', $companyId)
                ->where('status', 'pending')
                ->count(),
            'pending_tasks' => DB::table('tasks')
                ->join('jobs', 'tasks.job_id', '=', 'jobs.id')
                ->where('jobs.company_id', $companyId)
                ->where('tasks.status', 'pending')
                ->where('tasks.active', true)
                ->count(),
            'major_activities' => (clone $baseQuery)->where('is_major_activity', true)->count(),
            'activity_by_category' => (clone $baseQuery)
                ->groupBy('activity_category')
                ->selectRaw('activity_category, count(*) as count')
                ->pluck('count', 'activity_category')
                ->toArray(),
        ];
    }

    /**
     * Export project logs to Excel
     */
   public function export(Request $request)
{
    $user = Auth::user();
    $companyId = $user->company_id;
    
    // Build the same query as in projectLogs method
    $query = JobActivityLog::with(['job.jobType', 'job.client', 'job.equipment', 'user', 'affectedUser'])
        ->whereHas('job', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

    // Apply all the same filters as the view
    $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
    $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
    
    if ($dateFrom) {
        $query->whereDate('created_at', '>=', $dateFrom);
    }
    
    if ($dateTo) {
        $query->whereDate('created_at', '<=', $dateTo);
    }

    // Apply other filters (same as projectLogs method)
    if ($request->filled('job_id')) {
        $query->where('job_id', $request->job_id);
    }

    if ($request->filled('equipment_id')) {
        $query->whereHas('job', function($q) use ($request) {
            $q->where('equipment_id', $request->equipment_id);
        });
    }

    if ($request->filled('client_id')) {
        $query->whereHas('job', function($q) use ($request) {
            $q->where('client_id', $request->client_id);
        });
    }

    if ($request->filled('employee_id')) {
        $employeeUserId = Employee::find($request->employee_id)?->user_id;
        if ($employeeUserId) {
            $query->where(function($q) use ($employeeUserId) {
                $q->where('user_id', $employeeUserId)
                  ->orWhere('affected_user_id', $employeeUserId);
            });
        }
    }

    if ($request->filled('activity_type')) {
        $query->where('activity_type', $request->activity_type);
    }

    if ($request->filled('activity_category')) {
        $query->where('activity_category', $request->activity_category);
    }

    if ($request->filled('job_status')) {
        $query->whereHas('job', function($q) use ($request) {
            $q->where('status', $request->job_status);
        });
    }

    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('description', 'like', "%{$searchTerm}%")
              ->orWhere('activity_type', 'like', "%{$searchTerm}%");
        });
    }

    $query->orderBy('created_at', 'desc');
    $logs = $query->get();

    // Get summary statistics
    $stats = $this->getProjectLogStats($dateFrom, $dateTo, $companyId);
    
    // Get company information
    $company = Company::find($companyId);

    $data = [
        'logs' => $logs,
        'stats' => $stats,
        'company' => $company,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'filters' => $request->only(['job_id', 'equipment_id', 'client_id', 'employee_id', 'activity_type', 'activity_category', 'job_status', 'search']),
    ];

    $pdf = PDF::loadView('logs.pdf-export', $data);
    
    // Set paper size and orientation
    $pdf->setPaper('A4', 'landscape');
    
    $filename = 'project_logs_' . $dateFrom . '_to_' . $dateTo . '.pdf';
    
    return $pdf->download($filename);
}

    /**
     * Display the specified log.
     */
    public function show($id)
    {
        // Try to find in job activity logs first
        $log = JobActivityLog::with(['job.jobType', 'job.client', 'job.equipment', 'user', 'affectedUser'])
            ->find($id);
        
        if ($log) {
            // Check if log belongs to current user's company
            if ($log->job->company_id !== Auth::user()->company_id) {
                abort(403);
            }
            return view('logs.project-show', compact('log'));
        }
        
        // Fall back to system logs
        $log = Log::with(['user', 'userRole'])->findOrFail($id);
        return view('logs.show', compact('log'));
    }

    /**
     * Clear logs with optional filtering.
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

        return redirect()->route('logs.index')
            ->with('success', "{$count} logs have been cleared successfully.");
    }
}