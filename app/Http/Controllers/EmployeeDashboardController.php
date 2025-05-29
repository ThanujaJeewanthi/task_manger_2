<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\Employee;
use App\Models\JobEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee profile not found.');
        }

        // Employee-specific statistics
        $stats = [
            'my_active_jobs' => $this->getMyActiveJobs($employee->id)->count(),
            'my_completed_jobs' => $this->getMyCompletedJobs($employee->id)->count(),
            'my_pending_tasks' => $this->getMyPendingTasks($employee->id)->count(),
            'my_completed_tasks' => $this->getMyCompletedTasks($employee->id)->count(),
            'my_overdue_tasks' => $this->getMyOverdueTasks($employee->id)->count(),
            'total_assigned_jobs' => $this->getAllMyJobs($employee->id)->count(),
        ];

        // My current jobs
        $myActiveJobs = $this->getMyActiveJobs($employee->id)
            ->with(['jobType', 'client', 'equipment'])
            ->orderBy('priority', 'asc')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        // My current tasks
        $myActiveTasks = $this->getMyActiveTasks($employee->id)
            ->with(['job.jobType', 'job.client'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // My recent completed tasks
        $myRecentCompletedTasks = $this->getMyCompletedTasks($employee->id)
            ->with(['job.jobType', 'job.client'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Tasks by status for this employee
        $tasksByStatus = Task::whereHas('jobEmployees', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // My performance this month
        $thisMonthStats = [
            'jobs_completed' => $this->getMyCompletedJobs($employee->id)
                ->whereMonth('completed_date', Carbon::now()->month)
                ->whereYear('completed_date', Carbon::now()->year)
                ->count(),
            'tasks_completed' => $this->getMyCompletedTasks($employee->id)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->count(),
        ];

        // Upcoming deadlines for my jobs
        $upcomingDeadlines = $this->getMyActiveJobs($employee->id)
            ->with(['jobType', 'client'])
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->get();

        // My workload over time (last 6 months)
        $workloadTrends = JobEmployee::where('employee_id', $employee->id)
            ->join('tasks', 'job_employees.task_id', '=', 'tasks.id')
            ->select(
                DB::raw('DATE_FORMAT(job_employees.created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('job_employees.created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Alerts for employee
        $alerts = [];

        if ($stats['my_overdue_tasks'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "You have {$stats['my_overdue_tasks']} overdue tasks",
                'action' => 'View Overdue Tasks'
            ];
        }

        if ($upcomingDeadlines->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-calendar-alt',
                'message' => "{$upcomingDeadlines->count()} of your jobs are due within 7 days",
                'action' => 'View Upcoming Deadlines'
            ];
        }

        if ($stats['my_pending_tasks'] > 5) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-tasks',
                'message' => "You have {$stats['my_pending_tasks']} pending tasks",
                'action' => 'View Pending Tasks'
            ];
        }

        return view('dashboards.employee', compact(
            'employee',
            'stats',
            'myActiveJobs',
            'myActiveTasks',
            'myRecentCompletedTasks',
            'tasksByStatus',
            'thisMonthStats',
            'upcomingDeadlines',
            'workloadTrends',
            'alerts'
        ));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Check if this employee is assigned to this task
        $jobEmployee = JobEmployee::where('employee_id', $employee->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobEmployee) {
            return response()->json(['error' => 'You are not assigned to this task'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Update task status
        $task->update([
            'status' => $request->status,
            'updated_by' => $user->id
        ]);

        // Update job employee status and notes
        $jobEmployee->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $jobEmployee->notes,
            'updated_by' => $user->id
        ]);

        // If task is completed, update completion timestamp
        if ($request->status === 'completed') {
            $task->update(['completed_at' => Carbon::now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully'
        ]);
    }

    public function updateJobStatus(Request $request, Job $job)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Check if this employee is assigned to this job
        $isAssigned = JobEmployee::where('employee_id', $employee->id)
            ->whereHas('job', function ($query) use ($job) {
                $query->where('id', $job->id);
            })
            ->exists();

        if (!$isAssigned) {
            return response()->json(['error' => 'You are not assigned to this job'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'completed_date' => 'nullable|date'
        ]);

        $updateData = [
            'status' => $request->status,
            'updated_by' => $user->id
        ];

        // If job is completed, update completion date
        if ($request->status === 'completed') {
            $updateData['completed_date'] = $request->completed_date ?? Carbon::now();
        }

        $job->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Job status updated successfully'
        ]);
    }

    // Helper methods
    private function getMyActiveJobs($employeeId)
    {
        return Job::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('active', true)
          ->whereNotIn('status', ['completed', 'cancelled']);
    }

    private function getMyCompletedJobs($employeeId)
    {
        return Job::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('status', 'completed');
    }

    private function getAllMyJobs($employeeId)
    {
        return Job::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        });
    }

    private function getMyActiveTasks($employeeId)
    {
        return Task::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('active', true)
          ->whereIn('status', ['pending', 'in_progress']);
    }

    private function getMyPendingTasks($employeeId)
    {
        return Task::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('status', 'pending')->where('active', true);
    }

    private function getMyCompletedTasks($employeeId)
    {
        return Task::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('status', 'completed');
    }

    private function getMyOverdueTasks($employeeId)
    {
        return Task::whereHas('jobEmployees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)
                  ->where('end_date', '<', Carbon::now());
        })->where('active', true)
          ->whereNotIn('status', ['completed']);
    }
}
