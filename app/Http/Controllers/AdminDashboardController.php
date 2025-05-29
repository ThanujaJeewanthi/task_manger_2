<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Item;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Company-specific statistics
        $stats = [
            'total_jobs' => Job::where('company_id', $companyId)->count(),
            'active_jobs' => Job::where('company_id', $companyId)
                ->where('active', true)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'completed_jobs' => Job::where('company_id', $companyId)
                ->where('status', 'completed')->count(),
            'overdue_jobs' => Job::where('company_id', $companyId)
                ->where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'total_employees' => Employee::where('company_id', $companyId)->count(),
            'active_employees' => Employee::where('company_id', $companyId)
                ->where('active', true)->count(),
            'total_clients' => Client::where('company_id', $companyId)->count(),
            'active_clients' => Client::where('company_id', $companyId)
                ->where('active', true)->count(),
            'total_equipment' => Equipment::where('company_id', $companyId)->count(),
            'available_equipment' => Equipment::where('company_id', $companyId)
                ->where('status', 'available')->count(),
            'maintenance_equipment' => Equipment::where('company_id', $companyId)
                ->where('status', 'maintenance')->count(),
            'total_items' => Item::where('company_id', $companyId)->count(),
            'pending_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'pending')->count(),
            'in_progress_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'in_progress')->count(),
            'completed_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'completed')->count(),
            'total_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->count(),
        ];

        // Jobs by status for the company
        $jobsByStatus = Job::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Jobs by priority for the company
        $jobsByPriority = Job::where('company_id', $companyId)
            ->select(
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

        // Recent jobs for the company
        $recentJobs = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Jobs by job type
        $jobsByType = Job::with('jobType')
            ->where('company_id', $companyId)
            ->join('job_types', 'jobs.job_type_id', '=', 'job_types.id')
            ->select('job_types.name', DB::raw('count(jobs.id) as count'))
            ->groupBy('job_types.id', 'job_types.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get()
            ->pluck('count', 'name');

        // Employee performance (jobs completed)
        $employeePerformance = Employee::where('company_id', $companyId)
            ->withCount([
                'jobEmployees as completed_tasks' => function ($query) {
                    $query->whereHas('task', function ($q) {
                        $q->where('status', 'completed');
                    });
                },
                'jobEmployees as total_tasks'
            ])
            ->where('active', true)
            ->orderBy('completed_tasks', 'desc')
            ->take(10)
            ->get();

        // Monthly job trends for the company (last 6 months)
        $monthlyJobTrends = Job::where('company_id', $companyId)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Upcoming deadlines (next 7 days)
        $upcomingDeadlines = Job::with(['jobType', 'client'])
            ->where('company_id', $companyId)
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Alerts for company admin
        $alerts = [];

        if ($stats['overdue_jobs'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$stats['overdue_jobs']} jobs are overdue",
                'link' => route('jobs.index', ['filter' => 'overdue'])
            ];
        }

        if ($stats['maintenance_equipment'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-tools',
                'message' => "{$stats['maintenance_equipment']} equipment items need maintenance",
                'link' => route('equipments.index', ['status' => 'maintenance'])
            ];
        }

        if ($upcomingDeadlines->count() > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-calendar-alt',
                'message' => "{$upcomingDeadlines->count()} jobs due in the next 7 days",
                'link' => route('jobs.index', ['filter' => 'upcoming'])
            ];
        }

        // Active tasks summary
        $activeTasks = Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['job.jobType', 'jobEmployees.employee'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'jobsByStatus',
            'jobsByPriority',
            'recentJobs',
            'jobsByType',
            'employeePerformance',
            'monthlyJobTrends',
            'upcomingDeadlines',
            'alerts',
            'activeTasks'
        ));
    }

    public function getQuickStats()
    {
        $companyId = Auth::user()->company_id;

        return response()->json([
            'active_jobs' => Job::where('company_id', $companyId)
                ->where('active', true)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'pending_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'pending')->count(),
            'available_employees' => Employee::where('company_id', $companyId)
                ->where('active', true)
                ->count(),
            'overdue_jobs' => Job::where('company_id', $companyId)
                ->where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
        ]);
    }
}
