<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Task;
use App\Models\User;
use App\Models\JobUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JobActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserTaskController extends Controller
{
    /**
     * Start a task
     */
    public function startTask(Request $request, Task $task)
    {
        $user = Auth::user();
       


        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User record not found'], 403);
        }

        // Check if user is assigned to this task
        $jobUser = JobUser::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobUser) {
            return response()->json(['success' => false, 'message' => 'You are not assigned to this task'], 403);
        }

        if ($jobUser->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Task cannot be started'], 400);
        }

        try {
            DB::beginTransaction();

            // Update job user status for this specific user
            $jobUser->update([
                'status' => 'in_progress',
                'start_date' => now()->format('Y-m-d H:i:s'),
            ]);

            // Update task status to in_progress if any user started it
            if ($task->status === 'pending') {
                $task->update([
                    'status' => 'in_progress',
                    'updated_by' => Auth::id(),
                ]);
            }

            // Get the job instance
            $job = $task->job;

            // Auto-update job status based on tasks
            $this->updateJobStatusBasedOnTasks($job);

            DB::commit();

            // Log task started
            JobActivityLogger::logTaskStarted($job, $task, $user);

            return back()->with('success', 'Task started successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start task. Please try again.'
            ], 500);
        }
    }

    /**
     * Complete a task for the current user
     */
    public function completeTask(Request $request, Task $task)
{
    $request->validate([
        'completion_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $job = $task->job;
        $user = User::where('user_id', Auth::id())->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User record not found.'
            ], 403);
        }

        // Get the job user record for this specific user and task
        $jobUser = JobUser::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$jobUser) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this task.'
            ], 403);
        }

        if ($jobUser->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Task is already completed by you.'
            ], 400);
        }

        // UPDATED: Calculate actual completion time with time precision
        $completedAt = now();
        $actualDuration = null;
        
        if ($jobUser->start_date && $jobUser->start_time) {
            $startDateTime = \Carbon\Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
            $actualDuration = $startDateTime->floatDiffInRealDays($completedAt);
        }

        // Update task status in job_users for this specific user
        $jobUser->update([
            'status' => 'completed',
            'notes' => $request->completion_notes,
            'completed_at' => $completedAt, // ADDED: Track actual completion time
            'actual_duration' => $actualDuration, // ADDED: Track actual duration
            'updated_at' => now(),
        ]);

        // Check if ALL users assigned to this task have completed it
        $totalUsersForTask = JobUser::where('task_id', $task->id)->count();
        $completedUsersForTask = JobUser::where('task_id', $task->id)
            ->where('status', 'completed')
            ->count();

        // Update task status only if ALL users completed it
        if ($completedUsersForTask === $totalUsersForTask) {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Log task completion
            JobActivityLogger::logTaskCompleted($job, $task, $user, $request->completion_notes);
        }

        // Check if all tasks are completed to update job status
        $this->updateJobStatusBasedOnTasks($job);

        DB::commit();

        return back()->with('success', 'Task completed successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to complete task. Please try again.'
        ], 500);
    }
}
    /**
     * Update job status based on task completion
     */
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
                'completed_date' => now()->format('Y-m-d H:i:s'),
                'updated_by' => Auth::id(),
            ]);

            // Log job completion
            JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'All tasks completed');
            JobActivityLogger::logJobCompleted($job, 'All tasks have been completed successfully');
        }
        // If at least one task is in progress and job is approved, mark job as in_progress
        elseif ($inProgressTasks > 0 && $currentStatus === 'approved') {
            $newStatus = 'in_progress';
            $job->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
            ]);

            // Log status change
            JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'Tasks started');
        }
    }
}
