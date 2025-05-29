<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Item;
use App\Models\JobType;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // System-wide statistics
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('active', true)->count(),
            'total_users' => User::count(),
            'active_users' => User::where('active', true)->count(),
            'total_jobs' => Job::count(),
            'active_jobs' => Job::where('active', true)->count(),
            'completed_jobs' => Job::where('status', 'completed')->count(),
            'pending_jobs' => Job::where('status', 'pending')->count(),
            'in_progress_jobs' => Job::where('status', 'in_progress')->count(),
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('active', true)->count(),
            'total_clients' => Client::count(),
            'active_clients' => Client::where('active', true)->count(),
            'total_equipment' => Equipment::count(),
            'available_equipment' => Equipment::where('status', 'available')->count(),
            'total_items' => Item::count(),
            'total_job_types' => JobType::count(),
        ];

        // Recent activity logs
        $recentLogs = Log::with(['user', 'userRole'])
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Company performance data
        $companyStats = Company::withCount([
            'jobs',
            'jobs as completed_jobs_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'jobs as pending_jobs_count' => function ($query) {
                $query->where('status', 'pending');
            },
            'employees',
            'clients'
        ])->where('active', true)->take(10)->get();

        // Jobs by status chart data
        $jobsByStatus = Job::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Jobs by priority
        $jobsByPriority = Job::select(
            DB::raw('CASE
                WHEN priority = 1 THEN "High"
                WHEN priority = 2 THEN "Medium"
                WHEN priority = 3 THEN "Low"
                WHEN priority = 4 THEN "Very Low"
                ELSE "Unknown"
            END as priority_label'),
            DB::raw('count(*) as count')
        )
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority_label');

        // Monthly job trends (last 6 months)
        $monthlyJobTrends = Job::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Critical alerts
        $alerts = [];

        // Overdue jobs
        $overdueJobs = Job::where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($overdueJobs > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$overdueJobs} jobs are overdue",
                'link' => route('jobs.index', ['filter' => 'overdue'])
            ];
        }

        // Equipment in maintenance
        $maintenanceEquipment = Equipment::where('status', 'maintenance')->count();
        if ($maintenanceEquipment > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-tools',
                'message' => "{$maintenanceEquipment} equipment items need maintenance",
                'link' => route('equipments.index', ['status' => 'maintenance'])
            ];
        }

        // Recent high priority jobs
        $highPriorityJobs = Job::with(['jobType', 'client', 'company'])
            ->where('priority', 1)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.superadmin', compact(
            'stats',
            'recentLogs',
            'companyStats',
            'jobsByStatus',
            'jobsByPriority',
            'monthlyJobTrends',
            'alerts',
            'highPriorityJobs'
        ));
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'jobs_by_company':
                return Company::withCount('jobs')
                    ->where('active', true)
                    ->orderBy('jobs_count', 'desc')
                    ->take(10)
                    ->get()
                    ->map(function ($company) {
                        return [
                            'name' => $company->name,
                            'count' => $company->jobs_count
                        ];
                    });

            case 'monthly_registrations':
                return User::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();

            case 'equipment_status':
                return Equipment::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status');

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
