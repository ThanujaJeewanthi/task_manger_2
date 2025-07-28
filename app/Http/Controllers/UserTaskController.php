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
    public function startTask(Request $request, Task $task)
    {
        $user = Auth::user();

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

            $jobUser->update([
                'status' => 'in_progress',
                'start_date' => now()->format('Y-m-d H:i:s'),
            ]);

            if ($task->status === 'pending') {
                $task->update([
                    'status' => 'in_progress',
                    'updated_by' => Auth::id(),
                ]);
            }

            $job = $task->job;
            $this->updateJobStatusBasedOnTasks($job);

            DB::commit();

            JobActivityLogger::logUserTaskStarted($job, $task, $user);

            return back()->with('success', 'Task started successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start task. Please try again.'
            ], 500);
        }
    }

    public function completeTask(Request $request, Task $task)
    {
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $job = $task->job;
            $user = Auth::user();

            $jobUser = JobUser::where('user_id', $user->id)
                ->where('task_id', $task->id)
                ->first();

            if (!$jobUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User assignment not found.'
                ], 404);
            }

            $jobUser->update([
                'status' => 'completed',
                'notes' => $request->completion_notes,
                'end_date' => now()->format('Y-m-d'),
            ]);

            // Check if all assignees completed the task
            $allEmployeesCompleted = !$task->jobEmployees()->whereNotIn('status', ['completed', 'cancelled'])->exists();
            $allUsersCompleted = !$task->jobUsers()->whereNotIn('status', ['completed', 'cancelled'])->exists();

            if ($allEmployeesCompleted && $allUsersCompleted) {
                $task->update([
                    'status' => 'completed',
                    'updated_by' => Auth::id(),
                ]);
            }

            $this->updateJobStatusBasedOnTasks($job);

            DB::commit();

            JobActivityLogger::logUserTaskCompleted($job, $task, $user, $request->completion_notes);

            return back()->with('success', 'Task completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task. Please try again.'
            ], 500);
        }
    }

    private function updateJobStatusBasedOnTasks($job)
    {
        // Add logic similar to EmployeeTaskController's updateJobStatusBasedOnTasks method
        // This should check both employee and user task statuses
        $activeTasks = $job->tasks()->where('status', 'in_progress')->count();
        if ($activeTasks > 0) {
            $job->update(['status' => 'in_progress']);
        } else {
            $job->update(['status' => 'pending']);
        }
            
    }
}
