<?php

namespace App\Http\Controllers\Dashboard;

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
            'total_companies' => Company::where('active', true)->count(),
            'total_users' => User::where('active', true)->count(),
            'total_jobs' => Job::where('active', true)->count(),
            'total_employees' => Employee::where('active', true)->count(),
            'total_clients' => Client::where('active', true)->count(),
            'total_equipment' => Equipment::where('active', true)->count(),
            'total_items' => Item::where('active', true)->count(),
            'total_job_types' => JobType::where('active', true)->count(),
        ];

        // Job statistics
        $jobStats = [
            'pending_jobs' => Job::where('status', 'pending')->where('active', true)->count(),
            'in_progress_jobs' => Job::where('status', 'in_progress')->where('active', true)->count(),
            'completed_jobs' => Job::where('status', 'completed')->where('active', true)->count(),
            'overdue_jobs' => Job::where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)->count(),
            'high_priority_jobs' => Job::where('priority', 1)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)->count(),
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
            ->where('active', true)
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
            ->where('active', true)
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority_label');

        // Monthly job trends (last 6 months)
        $monthlyJobTrends = Job::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->where('active', true)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top performing companies
        $topCompanies = Company::withCount([
            'jobs as completed_jobs' => function ($query) {
                $query->where('status', 'completed');
            }
        ])
            ->where('active', true)
            ->orderBy('completed_jobs', 'desc')
            ->take(5)
            ->get();

        // Equipment status overview
        $equipmentStats = Equipment::select('status', DB::raw('count(*) as count'))
            ->where('active', true)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Critical alerts
        $alerts = [];

        if ($jobStats['overdue_jobs'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$jobStats['overdue_jobs']} jobs are overdue across all companies",
                'count' => $jobStats['overdue_jobs']
            ];
        }

        if ($jobStats['high_priority_jobs'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$jobStats['high_priority_jobs']} high priority jobs need attention",
                'count' => $jobStats['high_priority_jobs']
            ];
        }

        $maintenanceEquipment = $equipmentStats['maintenance'] ?? 0;
        if ($maintenanceEquipment > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-tools',
                'message' => "{$maintenanceEquipment} equipment items need maintenance",
                'count' => $maintenanceEquipment
            ];
        }

        // Recent high priority jobs across all companies
        $highPriorityJobs = Job::with(['jobType', 'client', 'company'])
            ->where('priority', 1)
            ->where('status', '!=', 'completed')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // System health metrics
        $systemHealth = [
            'active_companies_percentage' => Company::count() > 0 ?
                round((Company::where('active', true)->count() / Company::count()) * 100, 1) : 0,
            'job_completion_rate' => Job::count() > 0 ?
                round((Job::where('status', 'completed')->count() / Job::count()) * 100, 1) : 0,
            'employee_utilization' => Employee::where('active', true)->count() > 0 ?
                round((Employee::whereHas('jobEmployees', function($q) {
                    $q->whereIn('status', ['pending', 'in_progress']);
                })->count() / Employee::where('active', true)->count()) * 100, 1) : 0,
        ];

        return view('dashboard.superadmin', compact(
            'stats',
            'jobStats',
            'recentLogs',
            'companyStats',
            'jobsByStatus',
            'jobsByPriority',
            'monthlyJobTrends',
            'topCompanies',
            'equipmentStats',
            'alerts',
            'highPriorityJobs',
            'systemHealth'
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
                    ->where('active', true)
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status');

            case 'task_completion_trends':
                return Task::select(
                    DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                    ->where('status', 'completed')
                    ->where('updated_at', '>=', Carbon::now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();

            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
