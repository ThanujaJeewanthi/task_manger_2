<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Task;
use App\Models\Employee;
use App\Models\JobUser;
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
            'my_in_progress_tasks' => $this->getMyInProgressTasks($employee->id)->count(),
            'my_completed_tasks' => $this->getMyCompletedTasks($employee->id)->count(),
            'my_overdue_tasks' => $this->getMyOverdueTasks($employee->id)->count(),
        ];

        // Performance metrics
        $performanceStats = [
            'tasks_completed_this_week' => $this->getMyCompletedTasks($employee->id)
                ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'tasks_completed_this_month' => $this->getMyCompletedTasks($employee->id)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->count(),
            'average_task_completion_time' => $this->getAverageTaskCompletionTime($employee->id),
            'on_time_completion_rate' => $this->getOnTimeCompletionRate($employee->id),
        ];

        // My current active jobs with progress
        $myActiveJobs = $this->getMyActiveJobs($employee->id)
            ->with(['jobType', 'client', 'equipment'])
            ->orderBy('priority', 'asc')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($job) use ($employee) {
                $job->my_tasks_count = $this->getMyJobTasks($job->id, $employee->id)->count();
                $job->my_completed_tasks = $this->getMyJobTasks($job->id, $employee->id)
                    ->where('status', 'completed')->count();
                $job->progress = $job->my_tasks_count > 0 ?
                    round(($job->my_completed_tasks / $job->my_tasks_count) * 100, 1) : 0;
                return $job;
            });

        // My current tasks with details
        $myActiveTasks = $this->getMyActiveTasks($employee->id)
            ->with(['job.jobType', 'job.client', 'jobUsers' => function($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            }])
            ->whereHas('job') // Ensure job exists
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // My recent completed tasks
        $myRecentCompletedTasks = $this->getMyCompletedTasks($employee->id)
            ->with(['job.jobType', 'job.client'])
            ->whereHas('job') // Ensure job exists
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Tasks by status for this employee
        $tasksByStatus = Task::whereHas('jobUsers', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->select('status', DB::raw('count(*) as count'))
            ->where('active', true)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Upcoming deadlines for my jobs/tasks
        $upcomingDeadlines = $this->getMyActiveTasks($employee->id)
            ->with(['job.jobType', 'job.client', 'jobUsers' => function($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            }])
            ->whereHas('job') // Ensure job exists
            ->whereHas('jobUsers', function($query) use ($employee) {
                $query->where('employee_id', $employee->id)
                      ->where('end_date', '>=', Carbon::now())
                      ->where('end_date', '<=', Carbon::now()->addDays(7));
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // My workload over time (last 6 months)
        $workloadTrends = JobUser::where('employee_id', $employee->id)
            ->join('tasks', 'job_users.task_id', '=', 'tasks.id')
            ->select(
                DB::raw('DATE_FORMAT(job_users.created_at, "%Y-%m") as month'),
                DB::raw('count(*) as assigned_tasks'),
                DB::raw('sum(case when tasks.status = "completed" then 1 else 0 end) as completed_tasks')
            )
            ->where('job_users.created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Task completion trends by day for this month
        $dailyCompletionTrends = Task::whereHas('jobUsers', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->whereHas('job') // Ensure job exists
            ->where('status', 'completed')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('count(*) as completed_tasks')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Alerts for employee
        $alerts = [];

        if ($stats['my_overdue_tasks'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "You have {$stats['my_overdue_tasks']} overdue tasks",
                'count' => $stats['my_overdue_tasks'],
                'action' => 'View Overdue Tasks'
            ];
        }

        if ($upcomingDeadlines->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-calendar-alt',
                'message' => "{$upcomingDeadlines->count()} of your tasks are due within 7 days",
                'count' => $upcomingDeadlines->count(),
                'action' => 'View Upcoming Deadlines'
            ];
        }

        if ($stats['my_pending_tasks'] > 5) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-tasks',
                'message' => "You have {$stats['my_pending_tasks']} pending tasks",
                'count' => $stats['my_pending_tasks'],
                'action' => 'View Pending Tasks'
            ];
        }

        $inProgressTasks = $stats['my_in_progress_tasks'];
        if ($inProgressTasks > 3) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-clock',
                'message' => "You have {$inProgressTasks} tasks in progress",
                'count' => $inProgressTasks,
                'action' => 'Manage Active Tasks'
            ];
        }

        return view('dashboard.employee', compact(
            'employee',
            'stats',
            'performanceStats',
            'myActiveJobs',
            'myActiveTasks',
            'myRecentCompletedTasks',
            'tasksByStatus',
            'upcomingDeadlines',
            'workloadTrends',
            'dailyCompletionTrends',
            'alerts'
        ));
    }

    public function updateTaskStatus(Request $request, Task $task)
{
    $user = Auth::user();
    $employee = Employee::where('user_id', $user->id)->first();

    if (!$employee) {
        return response()->json(['success' => false, 'message' => 'Employee record not found'], 403);
    }

    $jobUser = JobUser::where('employee_id', $employee->id)
        ->where('task_id', $task->id)
        ->first();

    if (!$jobUser) {
        return response()->json(['success' => false, 'message' => 'Task assignment not found'], 404);
    }

    $request->validate([
        'status' => 'required|in:pending,in_progress,completed',
        'notes' => 'nullable|string|max:1000',
        'completion_notes' => 'nullable|string|max:1000'
    ]);

    DB::beginTransaction();
    try {
        // Update job employee status and notes for this specific employee
        $jobUserUpdateData = [
            'status' => $request->status,
            'updated_by' => $user->id
        ];

        if ($request->notes) {
            $jobUserUpdateData['notes'] = $request->notes;
        }

        if ($request->status === 'completed' && $request->completion_notes) {
            $jobUserUpdateData['notes'] = $request->completion_notes;
        }

        if ($request->status === 'in_progress' && !$jobUser->start_date) {
            $jobUserUpdateData['start_date'] = now()->toDateString();
        }

        $jobUser->update($jobUserUpdateData);

        // Handle task status updates based on ALL employee completions
        if ($request->status === 'completed') {
            // Check if ALL employees assigned to this task have completed it
            $totalEmployeesForTask = JobUser::where('task_id', $task->id)->count();
            $completedEmployeesForTask = JobUser::where('task_id', $task->id)
                ->where('status', 'completed')
                ->count();

            // Update task status only if ALL employees completed it
            if ($completedEmployeesForTask === $totalEmployeesForTask) {
                $task->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'updated_by' => $user->id
                ]);
            }
        } elseif ($request->status === 'in_progress' && $task->status === 'pending') {
            // Update task to in_progress if any employee starts it
            $task->update([
                'status' => 'in_progress',
                'updated_by' => $user->id
            ]);
        }

        // Update job status based on task completions
        $job = $task->job;
        if ($job) {
            $this->updateJobStatusBasedOnTasks($job);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'new_status' => $request->status,
            'job_status' => $job ? $job->fresh()->status : null,
            'task_status' => $task->fresh()->status
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update task status'
        ], 500);
    }
}
    public function updateJobStatus(Request $request, Job $job)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Check if this employee is assigned to this job
        $isAssigned = JobUser::where('employee_id', $employee->id)
            ->whereHas('job', function ($query) use ($job) {
                $query->where('id', $job->id);
            })
            ->exists();

        if (!$isAssigned) {
            return response()->json(['error' => 'You are not assigned to this job'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
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
            'message' => 'Job status updated successfully',
            'new_status' => $request->status
        ]);
    }
    private function updateJobStatusBasedOnTasks(Job $job)
{
    $tasks = $job->tasks()->where('active', true)->get();

    if ($tasks->isEmpty()) {
        return;
    }

    $totalTasks = $tasks->count();
    $completedTasks = $tasks->where('status', 'completed')->count();
    $inProgressTasks = $tasks->where('status', 'in_progress')->count();

    $currentStatus = $job->status;
    $newStatus = $currentStatus;

    // If all tasks are completed, mark job as completed
    if ($completedTasks === $totalTasks && $currentStatus !== 'completed') {
        $newStatus = 'completed';
        $job->update([
            'status' => $newStatus,
            'completed_date' => Carbon::now(),
            'updated_by' => Auth::id(),
        ]);

        // Log job completion
        \App\Services\JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'All tasks completed');
        \App\Services\JobActivityLogger::logJobCompleted($job, 'All tasks have been completed successfully');
    }
    // If at least one task is in progress and job is approved, mark job as in_progress
    elseif ($inProgressTasks > 0 && $currentStatus === 'approved') {
        $newStatus = 'in_progress';
        $job->update([
            'status' => $newStatus,
            'updated_by' => Auth::id(),
        ]);

        // Log status change
        \App\Services\JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'Tasks started');
    }
}

    // Helper methods
    private function getMyActiveJobs($employeeId)
    {
        return Job::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('active', true)
          ->whereNotIn('status', ['completed', 'cancelled']);
    }

    private function getMyCompletedJobs($employeeId)
    {
        return Job::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->where('status', 'completed');
    }

    private function getMyActiveTasks($employeeId)
    {
        return Task::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->whereHas('job', function ($query) {
            $query->where('active', true);
        })->where('active', true)
          ->whereIn('status', ['pending', 'in_progress']);
    }

    private function getMyPendingTasks($employeeId)
    {
        return Task::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->whereHas('job', function ($query) {
            $query->where('active', true);
        })->where('status', 'pending')->where('active', true);
    }

    private function getMyInProgressTasks($employeeId)
    {
        return Task::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->whereHas('job', function ($query) {
            $query->where('active', true);
        })->where('status', 'in_progress')->where('active', true);
    }

    private function getMyCompletedTasks($employeeId)
    {
        return Task::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->whereHas('job', function ($query) {
            $query->where('active', true);
        })->where('status', 'completed');
    }

    private function getMyOverdueTasks($employeeId)
    {
        return Task::whereHas('jobUsers', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)
                  ->where('end_date', '<', Carbon::now());
        })->whereHas('job', function ($query) {
            $query->where('active', true);
        })->where('active', true)
          ->whereNotIn('status', ['completed']);
    }

    private function getMyJobTasks($jobId, $employeeId)
    {
        return Task::where('job_id', $jobId)
            ->whereHas('jobUsers', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })->whereHas('job', function ($query) {
                $query->where('active', true);
            })->where('active', true);
    }

    private function getAverageTaskCompletionTime($employeeId)
    {
        $completedTasks = JobUser::where('employee_id', $employeeId)
            ->whereHas('task', function ($query) {
                $query->where('status', 'completed');
            })
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        if ($completedTasks->isEmpty()) {
            return 0;
        }

        $totalDays = $completedTasks->sum(function ($jobUser) {
            return Carbon::parse($jobUser->start_date)->diffInDays(Carbon::parse($jobUser->end_date)) + 1;
        });

        return round($totalDays / $completedTasks->count(), 1);
    }

   private function getOnTimeCompletionRate($employeeId)
{
    $completedTasks = \App\Models\JobUser::where('employee_id', $employeeId)
        ->where('status', 'completed')
        ->whereNotNull('end_date')
        ->get();

    if ($completedTasks->isEmpty()) {
        return 0;
    }

    $onTimeTasks = $completedTasks->filter(function ($jobUser) {
        // Check if the task was completed before or on the end date
        return $jobUser->updated_at <= \Carbon\Carbon::parse($jobUser->end_date)->endOfDay();
    })->count();

    return round(($onTimeTasks / $completedTasks->count()) * 100, 1);
}

    public function getTaskDetails(Request $request, Task $task)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        $jobUser = JobUser::where('employee_id', $employee->id)
            ->where('task_id', $task->id)
            ->with(['task.job.jobType', 'task.job.client'])
            ->first();

        if (!$jobUser) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        return response()->json([
            'task' => $task,
            'job' => $task->job,
            'assignment' => $jobUser,
            'progress' => $this->calculateTaskProgress($task, $employee->id)
        ]);
    }

private function calculateTaskProgress($task, $userId)
{
    $jobUser = \App\Models\JobUser::where('user_id', $userId)
        ->where('task_id', $task->id)
        ->first();

    if (!$jobUser) {
        return 0;
    }

    // If this user completed the task, return 100
    if ($jobUser->status === 'completed') {
        return 100;
    }

    // If task is pending for this user, return 0
    if ($jobUser->status === 'pending') {
        return 0;
    }

    // UPDATED: If in progress, calculate time-based progress with time precision
    if ($jobUser->status === 'in_progress' && $jobUser->start_date && $jobUser->end_date) {
        // UPDATED: Use time components if available
        $startTime = $jobUser->start_time ? $jobUser->start_time->format('H:i:s') : '00:00:00';
        $endTime = $jobUser->end_time ? $jobUser->end_time->format('H:i:s') : '23:59:59';

        $startDateTime = \Carbon\Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $startTime);
        $endDateTime = \Carbon\Carbon::parse($jobUser->end_date->format('Y-m-d') . ' ' . $endTime);
        $today = \Carbon\Carbon::now();

        if ($today >= $endDateTime) {
            return 90; // Overdue but not completed
        }

        if ($today <= $startDateTime) {
            return 10; // Just started
        }

        // UPDATED: Use real hours difference for more precise calculation
        $totalHours = $startDateTime->diffInRealHours($endDateTime);
        if ($totalHours <= 0) return 50; // Same time task

        $elapsedHours = $startDateTime->diffInRealHours($today);
        return round(($elapsedHours / $totalHours) * 90); // Max 90% for time-based progress
    }

    return 25; // Default for in-progress without dates
}
}
