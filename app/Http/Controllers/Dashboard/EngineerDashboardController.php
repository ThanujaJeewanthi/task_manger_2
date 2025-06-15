<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Item;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EngineerDashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Engineer-specific statistics
        $stats = [
            'total_jobs' => Job::where('company_id', $companyId)->where('active', true)->count(),
            'jobs_pending_approval' => Job::where('company_id', $companyId)
                ->where('approval_status', 'requested')->where('active', true)->count(),
            'jobs_approved_by_me' => Job::where('company_id', $companyId)
                ->where('approved_by', Auth::id())->count(),
            'total_employees' => Employee::where('company_id', $companyId)->where('active', true)->count(),
            'total_equipment' => Equipment::where('company_id', $companyId)->where('active', true)->count(),
            'maintenance_equipment' => Equipment::where('company_id', $companyId)
                ->where('status', 'maintenance')->where('active', true)->count(),
        ];

        // Job status statistics
        $jobStats = [
            'pending_jobs' => Job::where('company_id', $companyId)
                ->where('status', 'pending')->where('active', true)->count(),
            'in_progress_jobs' => Job::where('company_id', $companyId)
                ->where('status', 'in_progress')->where('active', true)->count(),
            'completed_jobs' => Job::where('company_id', $companyId)
                ->where('status', 'completed')->count(),
            'overdue_jobs' => Job::where('company_id', $companyId)
                ->where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)->count(),
            'high_priority_jobs' => Job::where('company_id', $companyId)
                ->where('priority', 1)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)->count(),
        ];

        // Task statistics
        $taskStats = [
            'pending_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'pending')->where('active', true)->count(),

            'in_progress_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'in_progress')->where('active', true)->count(),

            'completed_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'completed')->count(),
        ];

        // Jobs requiring approval
        $jobsForApproval = Job::with(['jobType', 'client', 'equipment', 'jobItems'])
            ->where('company_id', $companyId)
            ->where('approval_status', 'requested')
            ->where('request_approval_from', Auth::id())
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Jobs by status for the company
        $jobsByStatus = Job::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'))
            ->where('active', true)
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
            ->where('active', true)
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority_label');

        // Recent jobs for the company
        $recentJobs = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Equipment status for the company
        $equipmentStats = Equipment::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'))
            ->where('active', true)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Employee performance (tasks completed this month)
        $employeePerformance = Employee::where('company_id', $companyId)
            ->withCount([
                'jobEmployees as completed_tasks_this_month' => function ($query) {
                    $query->whereHas('task', function ($q) {
                        $q->where('status', 'completed')
                          ->whereMonth('updated_at', Carbon::now()->month)
                          ->whereYear('updated_at', Carbon::now()->year);
                    });
                },
                'jobEmployees as total_active_tasks' => function ($query) {
                    $query->whereHas('task', function ($q) {
                        $q->whereIn('status', ['pending', 'in_progress'])
                          ->where('active', true);
                    });
                }
            ])
            ->where('active', true)
            ->orderBy('completed_tasks_this_month', 'desc')
            ->take(10)
            ->get();

        // Upcoming deadlines (next 7 days)
        $upcomingDeadlines = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('active', true)
            ->orderBy('due_date', 'asc')
            ->get();

        // Active tasks summary with employee assignments
        $activeTasks = Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('active', true)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['job.jobType', 'job.client', 'jobEmployees.employee'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Client job distribution
        $clientJobStats = Client::where('company_id', $companyId)
            ->withCount([
                'jobs as total_jobs',
                'jobs as completed_jobs' => function ($query) {
                    $query->where('status', 'completed');
                },
                'jobs as pending_jobs' => function ($query) {
                    $query->where('status', 'pending');
                }
            ])
            ->where('active', true)
            ->orderBy('total_jobs', 'desc')
            ->take(8)
            ->get();

        // Engineer-specific alerts
        $alerts = [];

        if ($stats['jobs_pending_approval'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clipboard-check',
                'message' => "{$stats['jobs_pending_approval']} jobs require your approval",
                'count' => $stats['jobs_pending_approval'],
                'action' => 'View Approval Queue'
            ];
        }

        if ($jobStats['overdue_jobs'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$jobStats['overdue_jobs']} jobs are overdue",
                'count' => $jobStats['overdue_jobs'],
                'action' => 'View Overdue Jobs'
            ];
        }

        $maintenanceEquipment = $stats['maintenance_equipment'];
        if ($maintenanceEquipment > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-tools',
                'message' => "{$maintenanceEquipment} equipment items need maintenance",
                'count' => $maintenanceEquipment,
                'action' => 'View Equipment'
            ];
        }

        if ($upcomingDeadlines->count() > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-calendar-alt',
                'message' => "{$upcomingDeadlines->count()} jobs due in the next 7 days",
                'count' => $upcomingDeadlines->count(),
                'action' => 'View Upcoming Jobs'
            ];
        }

        if ($jobStats['high_priority_jobs'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$jobStats['high_priority_jobs']} high priority jobs need attention",
                'count' => $jobStats['high_priority_jobs'],
                'action' => 'View High Priority Jobs'
            ];
        }

        // Monthly job trends for the company (last 6 months)
        $monthlyJobTrends = Job::where('company_id', $companyId)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->where('active', true)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.engineer', compact(
            'stats',
            'jobStats',
            'taskStats',
            'jobsForApproval',
            'equipmentStats',
            'jobsByStatus',
            'jobsByPriority',
            'recentJobs',
            'employeePerformance',
            'monthlyJobTrends',
            'upcomingDeadlines',
            'activeTasks',
            'clientJobStats',
            'alerts'
        ));
    }

    public function approveJob(Request $request, Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Check if user has approval rights
        $userRole = Auth::user()->userRole->name ?? '';
        if (!in_array($userRole, ['Engineer', 'admin'])) {
            abort(403, 'You do not have permission to approve jobs.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                $job->update([
                    'approval_status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'approval_notes' => $request->approval_notes,
                    'status' => 'pending', // Move to pending for task assignment
                    'updated_by' => Auth::id(),
                ]);

                $message = 'Job approved successfully. You can now add tasks.';
            } else {
                $job->update([
                    'approval_status' => 'rejected',
                    'rejected_by' => Auth::id(),
                    'rejected_at' => now(),
                    'rejection_notes' => $request->approval_notes,
                    'updated_by' => Auth::id(),
                ]);

                $message = 'Job rejected successfully.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process approval'], 500);
        }
    }

    public function getQuickStats()
    {
        $companyId = Auth::user()->company_id;

        return response()->json([
            'pending_approvals' => Job::where('company_id', $companyId)
                ->where('approval_status', 'requested')
                ->where('request_approval_from', Auth::id())
                ->where('active', true)
                ->count(),
            'active_jobs' => Job::where('company_id', $companyId)
                ->where('active', true)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'pending_tasks' => Task::whereHas('job', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('status', 'pending')->where('active', true)->count(),
            'overdue_jobs' => Job::where('company_id', $companyId)
                ->where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)
                ->count(),
        ]);
    }
}
