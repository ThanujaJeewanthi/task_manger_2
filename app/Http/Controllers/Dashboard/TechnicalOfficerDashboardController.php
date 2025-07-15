<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TechnicalOfficerDashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Get assigned jobs for this technical officer
        $assignedJobs = Job::where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('active', true);

        // Technical Officer specific statistics
        $stats = [
            'my_assigned_jobs' => $assignedJobs->count(),
            'my_pending_jobs' => $assignedJobs->where('status', 'pending')->count(),
            'my_in_progress_jobs' => $assignedJobs->where('status', 'in_progress')->count(),
            'my_completed_jobs' => $assignedJobs->where('status', 'completed')->count(),
            'jobs_awaiting_approval' => Job::where('company_id', $companyId)
                ->where('approval_status', 'requested')
                ->where('created_by', Auth::id())
                ->where('active', true)->count(),
            'total_equipment' => Equipment::where('company_id', $companyId)->where('active', true)->count(),
        ];

        // Performance metrics for this technical officer
        $performanceStats = [
            'jobs_completed_this_week' => Job::where('company_id', $companyId)
                ->where('assigned_user_id', Auth::id())
                ->where('status', 'completed')
                ->whereBetween('completed_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'jobs_completed_this_month' => Job::where('company_id', $companyId)
                ->where('assigned_user_id', Auth::id())
                ->where('status', 'completed')
                ->whereMonth('completed_date', Carbon::now()->month)
                ->whereYear('completed_date', Carbon::now()->year)
                ->count(),
            'average_completion_time' => $this->getAverageJobCompletionTime(),
            'on_time_completion_rate' => $this->getOnTimeCompletionRate(),
        ];

        // My assigned jobs with progress
        $myAssignedJobs = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('active', true)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('approval_status', null)
            ->orderBy('priority', 'asc')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($job) {
                $job->tasks_count = $job->tasks()->where('active', true)->count();
                $job->completed_tasks = $job->tasks()->where('status', 'completed')->where('active', true)->count();
                $job->progress = $job->tasks_count > 0 ?
                    round(($job->completed_tasks / $job->tasks_count) * 100, 1) : 0;
                return $job;
            });

        // Jobs by status for this technical officer
        $jobsByStatus = Job::where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->select('status', DB::raw('count(*) as count'))
            ->where('active', true)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Equipment status in the company
        $equipmentStats = Equipment::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'))
            ->where('active', true)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent jobs assigned to me
        $recentJobs = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Upcoming deadlines for my jobs
        $upcomingDeadlines = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('active', true)
            ->orderBy('due_date', 'asc')
            ->get();

        // Jobs requiring items and approval
        $jobsRequiringApproval = Job::with(['jobType', 'client', 'equipment', 'jobItems'])
            ->where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('approval_status', 'requested')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Work trends (last 6 months)
        $workTrends = Job::where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as assigned_jobs'),
                DB::raw('sum(case when status = "completed" then 1 else 0 end) as completed_jobs')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Technical Officer specific alerts
        $alerts = [];

        if ($stats['jobs_awaiting_approval'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-clock',
                'message' => "{$stats['jobs_awaiting_approval']} of your jobs are awaiting approval",
                'count' => $stats['jobs_awaiting_approval'],
                'action' => 'View Pending Approvals'
            ];
        }

        $overdueJobs = Job::where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('due_date', '<', Carbon::now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('active', true)->count();

        if ($overdueJobs > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$overdueJobs} of your jobs are overdue",
                'count' => $overdueJobs,
                'action' => 'View Overdue Jobs'
            ];
        }

        if ($upcomingDeadlines->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-calendar-alt',
                'message' => "{$upcomingDeadlines->count()} of your jobs are due within 7 days",
                'count' => $upcomingDeadlines->count(),
                'action' => 'View Upcoming Deadlines'
            ];
        }

        $highPriorityJobs = Job::where('company_id', $companyId)
            ->where('assigned_user_id', Auth::id())
            ->where('priority', 1)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('active', true)->count();

        if ($highPriorityJobs > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$highPriorityJobs} high priority jobs need your attention",
                'count' => $highPriorityJobs,
                'action' => 'View High Priority Jobs'
            ];
        }

        return view('dashboard.technical-officer', compact(
            'stats',
            'performanceStats',
            'myAssignedJobs',
            'jobsByStatus',
            'equipmentStats',
            'recentJobs',
            'upcomingDeadlines',
            'jobsRequiringApproval',
            'workTrends',
            'alerts'
        ));
    }

    public function completeJob(Request $request, Job $job)
    {
        // Check if job belongs to current user's company and is assigned to them
        if ($job->company_id !== Auth::user()->company_id || $job->assigned_user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
            'is_minor_issue' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            if ($request->boolean('is_minor_issue')) {
                // Complete job as minor issue
                $job->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                    'completion_notes' => $request->completion_notes,
                    'approval_status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'approval_notes' => 'Completed as minor issue',
                    'updated_by' => Auth::id(),
                ]);
            } else {
                // Job requires items/approval
                $job->update([
                    'status' => 'pending',
                    'completion_notes' => $request->completion_notes,
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->boolean('is_minor_issue') ?
                    'Job completed successfully.' :
                    'Job status updated. You can now add items if needed.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update job status'], 500);
        }
    }

    public function updateJobStatus(Request $request, Job $job)
    {
        // Check if job belongs to current user's company and is assigned to them
        if ($job->company_id !== Auth::user()->company_id || $job->assigned_user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,on_hold,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        $updateData = [
            'status' => $request->status,
            'updated_by' => Auth::id()
        ];

        if ($request->status === 'completed') {
            $updateData['completed_date'] = now();
        }

        $job->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Job status updated successfully',
            'new_status' => $request->status
        ]);
    }

    private function getAverageJobCompletionTime()
    {
        $completedJobs = Job::where('company_id', Auth::user()->company_id)
            ->where('assigned_user_id', Auth::id())
            ->where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('completed_date')
            ->get();

        if ($completedJobs->isEmpty()) {
            return 0;
        }

        $totalDays = $completedJobs->sum(function ($job) {
            return Carbon::parse($job->start_date)->diffInDays(Carbon::parse($job->completed_date)) + 1;
        });

        return round($totalDays / $completedJobs->count(), 1);
    }

    private function getOnTimeCompletionRate()
    {
        $completedJobs = Job::where('company_id', Auth::user()->company_id)
            ->where('assigned_user_id', Auth::id())
            ->where('status', 'completed')
            ->whereNotNull('due_date')
            ->whereNotNull('completed_date')
            ->get();

        if ($completedJobs->isEmpty()) {
            return 0;
        }

        $onTimeJobs = $completedJobs->filter(function ($job) {
            return Carbon::parse($job->completed_date) <= Carbon::parse($job->due_date);
        })->count();

        return round(($onTimeJobs / $completedJobs->count()) * 100, 1);
    }

    public function getQuickStats()
    {
        $companyId = Auth::user()->company_id;

        return response()->json([
            'my_assigned_jobs' => Job::where('company_id', $companyId)
                ->where('assigned_user_id', Auth::id())
                ->where('active', true)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'pending_jobs' => Job::where('company_id', $companyId)
                ->where('assigned_user_id', Auth::id())
                ->where('status', 'pending')
                ->where('active', true)
                ->count(),
            'jobs_awaiting_approval' => Job::where('company_id', $companyId)
                ->where('approval_status', 'requested')
                ->where('created_by', Auth::id())
                ->where('active', true)
                ->count(),
            'overdue_jobs' => Job::where('company_id', $companyId)
                ->where('assigned_user_id', Auth::id())
                ->where('due_date', '<', Carbon::now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('active', true)
                ->count(),
        ]);
    }
}
