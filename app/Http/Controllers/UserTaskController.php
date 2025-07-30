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

        // Check if user is assigned to this task
        $jobUser = JobUser::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobUser) {
            return back()->with('error', 'You are not assigned to this task.');
        }

        if ($jobUser->status !== 'pending') {
            return back()->with('error', 'Task cannot be started from its current status.');
        }

        try {
            DB::beginTransaction();

            // Update job user status for this specific user
            $jobUser->update([
                'status' => 'in_progress',
                'updated_by' => Auth::id(),
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
            \Log::error('Error starting task: ' . $e->getMessage());
            return back()->with('error', 'Failed to start task. Please try again.');
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
            $user = Auth::user();

            // Check if user is assigned to this task
            $jobUser = JobUser::where('user_id', $user->id)
                ->where('task_id', $task->id)
                ->first();

            if (!$jobUser) {
                return back()->with('error', 'You are not assigned to this task.');
            }

            if ($jobUser->status !== 'in_progress') {
                return back()->with('error', 'Task cannot be completed from its current status.');
            }

            // Update the user's task status
            $jobUser->update([
                'status' => 'completed',
                'notes' => $request->completion_notes,
                'updated_by' => Auth::id(),
            ]);

            // Check if all users assigned to this task have completed it
            $allTaskUsers = JobUser::where('task_id', $task->id)->get();
            $allCompleted = $allTaskUsers->every(function ($ju) {
                return $ju->status === 'completed';
            });

            // If all users completed the task, update task status
            if ($allCompleted) {
                $task->update([
                    'status' => 'completed',
                    'updated_by' => Auth::id(),
                ]);

                // Auto-update job status based on tasks
                $this->updateJobStatusBasedOnTasks($job);
            }

            DB::commit();

            // Log task completion
            JobActivityLogger::logTaskCompleted($job, $task, $user);

            return back()->with('success', 'Task completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error completing task: ' . $e->getMessage());
            return back()->with('error', 'Failed to complete task. Please try again.');
        }
    }

    /**
     * Update job status based on task completion
     */
    private function updateJobStatusBasedOnTasks($job)
    {
        $jobTasks = $job->tasks()->where('active', true)->get();

        if ($jobTasks->isEmpty()) {
            return;
        }

        $allCompleted = $jobTasks->every(function ($task) {
            return $task->status === 'completed';
        });

        $anyInProgress = $jobTasks->contains(function ($task) {
            return $task->status === 'in_progress';
        });

        if ($allCompleted && $job->status !== 'completed') {
            $job->update([
                'status' => 'completed',
                'updated_by' => Auth::id()
            ]);
        } elseif ($anyInProgress && $job->status === 'pending') {
            $job->update([
                'status' => 'in_progress',
                'updated_by' => Auth::id()
            ]);
        }
    }

    /**
     * Get task details for API calls
     */
    public function getTaskDetails(Request $request, Task $task)
    {
        $user = Auth::user();

        $jobUser = JobUser::where('user_id', $user->id)
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
            'progress' => $this->calculateTaskProgress($task, $user->id)
        ]);
    }

    /**
     * Calculate task progress for a specific user
     */
    private function calculateTaskProgress($task, $userId)
    {
        $jobUser = JobUser::where('user_id', $userId)
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

        // If in progress, calculate time-based progress with time precision
        if ($jobUser->status === 'in_progress' && $jobUser->start_date && $jobUser->end_date) {
            // Use time components if available
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

            // Use real hours difference for more precise calculation
            $totalHours = $startDateTime->diffInRealHours($endDateTime);
            if ($totalHours <= 0) return 50; // Same time task

            $elapsedHours = $startDateTime->diffInRealHours($today);
            return round(($elapsedHours / $totalHours) * 90); // Max 90% for time-based progress
        }

        return 25; // Default for in-progress without dates
    }

    /**
     * Update task status (for API calls)
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        $jobUser = JobUser::where('user_id', $user->id)
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
            // Update job user status and notes for this specific user
            $jobUserUpdateData = [
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ];

            if ($request->has('notes')) {
                $jobUserUpdateData['notes'] = $request->notes;
            }

            if ($request->has('completion_notes') && $request->status === 'completed') {
                $jobUserUpdateData['notes'] = $request->completion_notes;
            }

            $jobUser->update($jobUserUpdateData);

            // Update task status based on all assigned users
            $this->updateTaskStatusBasedOnUsers($task);

            // Update job status based on tasks
            $this->updateJobStatusBasedOnTasks($task->job);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => $task->fresh(),
                'jobUser' => $jobUser->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating task status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status'
            ], 500);
        }
    }

    /**
     * Update task status based on all assigned users' statuses
     */
    private function updateTaskStatusBasedOnUsers($task)
    {
        $allJobUsers = JobUser::where('task_id', $task->id)->get();

        if ($allJobUsers->isEmpty()) {
            return;
        }

        $allCompleted = $allJobUsers->every(function ($ju) {
            return $ju->status === 'completed';
        });

        $anyInProgress = $allJobUsers->contains(function ($ju) {
            return $ju->status === 'in_progress';
        });

        if ($allCompleted && $task->status !== 'completed') {
            $task->update([
                'status' => 'completed',
                'updated_by' => Auth::id()
            ]);
        } elseif ($anyInProgress && $task->status === 'pending') {
            $task->update([
                'status' => 'in_progress',
                'updated_by' => Auth::id()
            ]);
        }
    }
}
