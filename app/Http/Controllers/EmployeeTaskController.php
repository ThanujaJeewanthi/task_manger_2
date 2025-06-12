<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Job\JobController;
use App\Models\Task;
use App\Models\JobEmployee;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            return response()->json(['error' => 'Employee record not found'], 403);
        }

        // Check if employee is assigned to this task
        $jobEmployee = JobEmployee::where('employee_id', $employee->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobEmployee) {
            return response()->json(['error' => 'You are not assigned to this task'], 403);
        }

        if ($task->status !== 'pending') {
            return response()->json(['error' => 'Task cannot be started'], 400);
        }

        try {
            DB::beginTransaction();

            // Update task status
            $task->update([
                'status' => 'in_progress',
                'updated_by' => Auth::id(),
            ]);

            // Update job employee status
            $jobEmployee->update([
                'status' => 'in_progress',
                'start_date' => now()->toDateString(),
            ]);

            // Auto-update job status
            $jobController = new JobController();
            $jobController->updateJobStatusBasedOnTasks($task->job);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully',
                'new_status' => 'in_progress'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to start task'], 500);
        }
    }

    /**
     * Complete a task
     */
    public function completeTask(Request $request, Task $task)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee record not found'], 403);
        }

        // Check if employee is assigned to this task
        $jobEmployee = JobEmployee::where('employee_id', $employee->id)
            ->where('task_id', $task->id)
            ->first();

        if (!$jobEmployee) {
            return response()->json(['error' => 'You are not assigned to this task'], 403);
        }

        if (!in_array($task->status, ['pending', 'in_progress'])) {
            return response()->json(['error' => 'Task cannot be completed'], 400);
        }

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update task status
            $task->update([
                'status' => 'completed',
                'updated_by' => Auth::id(),
            ]);

            // Update job employee status
            $jobEmployee->update([
                'status' => 'completed',
                'notes' => $request->completion_notes,
                'end_date' => now()->toDateString(),
            ]);

            // Auto-update job status
            $jobController = new JobController();
            $jobController->updateJobStatusBasedOnTasks($task->job);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'new_status' => 'completed'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to complete task'], 500);
        }
    }
}
