<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Task;
use App\Models\Employee;
use App\Models\JobEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JobActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EmployeeTaskController extends Controller
{
    /**
     * Start a task
     */
    public function startTask(Request $request, Task $task)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee record not found'], 403);
        }

        // Check if employee is assigned to this task
        $jobEmployee = JobEmployee::where('employee_id', $employee->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobEmployee) {
            return response()->json(['success' => false, 'message' => 'You are not assigned to this task'], 403);
        }

        if ($jobEmployee->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Task cannot be started'], 400);
        }

        try {
            DB::beginTransaction();

            // Update job employee status for this specific employee
            $jobEmployee->update([
                'status' => 'in_progress',
                'start_date' => now()->toDateString(),
            ]);

            // Update task status to in_progress if any employee started it
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
            JobActivityLogger::logTaskStarted($job, $task, $employee);

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
     * Complete a task for the current employee
     */
    public function completeTask(Request $request, Task $task)
    {
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $job = $task->job;
            $employee = Employee::where('user_id', Auth::id())->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found.'
                ], 403);
            }

            // Get the job employee record for this specific employee and task
            $jobEmployee = JobEmployee::where('task_id', $task->id)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$jobEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this task.'
                ], 403);
            }

            if ($jobEmployee->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Task is already completed by you.'
                ], 400);
            }

            // Update task status in job_employees for this specific employee
            $jobEmployee->update([
                'status' => 'completed',
                'notes' => $request->completion_notes,
                'updated_at' => now(),
            ]);

            // Check if ALL employees assigned to this task have completed it
            $totalEmployeesForTask = JobEmployee::where('task_id', $task->id)->count();
            $completedEmployeesForTask = JobEmployee::where('task_id', $task->id)
                ->where('status', 'completed')
                ->count();

            // Update task status only if ALL employees completed it
            if ($completedEmployeesForTask === $totalEmployeesForTask) {
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

                // Log task completion
                JobActivityLogger::logTaskCompleted($job, $task, $employee, $request->completion_notes);
            }

            // Check if all tasks are completed to update job status
            $this->updateJobStatusBasedOnTasks($job);

            DB::commit();

            // return back with error or sucess message
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
                'completed_date' => now(),
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
