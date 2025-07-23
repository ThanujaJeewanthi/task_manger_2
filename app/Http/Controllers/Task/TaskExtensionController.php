<?php

namespace App\Http\Controllers\Task;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\Task;
use App\Models\Employee;
use App\Models\JobEmployee;
use App\Models\TaskUserAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JobActivityLogger;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TaskExtensionRequest;
use Illuminate\Support\Facades\Auth;

class TaskExtensionController extends Controller
{
    /**
     * Display extension requests for approval (Supervisors/TOs)
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $userRole = Auth::user()->userRole->name ?? '';

        // Check if user can approve extensions
        if (!in_array($userRole, ['Supervisor', 'Technical Officer', 'Engineer'])) {
            abort(403, 'You do not have permission to approve task extensions.');
        }

        $query = TaskExtensionRequest::with([
            'job',
            'task',
            'employee.user',
            'requestedBy'
        ])->forCompany($companyId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->pending(); // Default to pending requests
        }

        // Additional filtering for supervisors (only their created jobs)
        if ($userRole === 'Supervisor') {
            $query->forSupervisor(Auth::id());
        }

        $extensionRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tasks.extension.index', compact('extensionRequests'));
    }
// Update the create method
public function create(Task $task)
{
    $currentUser = Auth::user();

    // Check if user is assigned to this task (either through user assignment or employee assignment)
    $userAssignment = TaskUserAssignment::where('task_id', $task->id)
        ->where('user_id', $currentUser->id)
        ->where('active', true)
        ->first();

    $employeeAssignment = null;
    if (!$userAssignment) {
        $employee = Employee::where('user_id', $currentUser->id)->first();
        if ($employee) {
            $employeeAssignment = JobEmployee::where('task_id', $task->id)
                ->where('employee_id', $employee->id)
                ->first();
        }
    }

    if (!$userAssignment && !$employeeAssignment) {
        abort(403, 'You are not assigned to this task.');
    }

    // Get the job of this task
    $job = Job::findOrFail($task->job_id);

    // Check if task is not completed
    if ($task->status === 'completed') {
        return redirect()->back()->with('error', 'Cannot request extension for completed task.');
    }

    // Check if there's already a pending request for this task
    $existingRequest = TaskExtensionRequest::where('task_id', $task->id)
        ->where(function ($query) use ($currentUser, $employee) {
            $query->where('user_id', $currentUser->id);
            if ($employee) {
                $query->orWhere('employee_id', $employee->id);
            }
        })
        ->where('status', 'pending')
        ->first();

    if ($existingRequest) {
        return redirect()->back()->with('error', 'You already have a pending extension request for this task.');
    }

    return view('tasks.extension.create', compact('task', 'job', 'userAssignment', 'employeeAssignment'));
}

// Update the store method
public function requestTaskExtension(Request $request, Task $task)
{
    $request->validate([
        'requested_end_date' => 'required|date|after:current_end_date',
        'reason' => 'required|string|max:500',
        'justification' => 'nullable|string|max:1000',
    ]);

    $currentUser = Auth::user();

    // Determine which type of assignment this is
    $userAssignment = TaskUserAssignment::where('task_id', $task->id)
        ->where('user_id', $currentUser->id)
        ->where('active', true)
        ->first();

    $employee = null;
    $employeeAssignment = null;

    if (!$userAssignment) {
        $employee = Employee::where('user_id', $currentUser->id)->first();
        if ($employee) {
            $employeeAssignment = JobEmployee::where('task_id', $task->id)
                ->where('employee_id', $employee->id)
                ->first();
        }
    }

    if (!$userAssignment && !$employeeAssignment) {
        abort(403, 'You are not assigned to this task.');
    }

    $currentEndDate = $userAssignment ? $userAssignment->end_date : $employeeAssignment->end_date;

    try {
        DB::beginTransaction();

        $extensionRequest = TaskExtensionRequest::create([
            'job_id' => $task->job_id,
            'task_id' => $task->id,
            'user_id' => $userAssignment ? $currentUser->id : null,
            'employee_id' => $employeeAssignment ? $employee->id : null,
            'requested_by' => $currentUser->id,
            'current_end_date' => $currentEndDate,
            'requested_end_date' => $request->requested_end_date,
            'reason' => $request->reason,
            'justification' => $request->justification,
            'status' => 'pending',
            'created_by' => $currentUser->id,
            'updated_by' => $currentUser->id,
        ]);

        // Log the extension request
        if ($userAssignment) {
            JobActivityLogger::logTaskExtensionRequestedByUser(
                $task->job,
                $task,
                $currentUser,
                $currentEndDate,
                $request->requested_end_date,
                $request->reason
            );
        } else {
            JobActivityLogger::logTaskExtensionRequested(
                $task->job,
                $task,
                $employee,
                $currentEndDate,
                $request->requested_end_date,
                $request->reason
            );
        }

        DB::commit();

        return redirect()->route('jobs.show', $task->job)
                        ->with('success', 'Extension request submitted successfully. Your request is pending approval.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Task extension request failed: ' . $e->getMessage());
        return redirect()->route('tasks.extension.create', $task)
            ->with('error', 'Failed to submit extension request. Please try again.')
            ->withInput();
    }
}
    /**
     * Show specific extension request
     */
    public function show(TaskExtensionRequest $extensionRequest)
    {
        $companyId = Auth::user()->company_id;

        // Check if request belongs to user's company
        if ($extensionRequest->job->company_id !== $companyId) {
            abort(403);
        }

        $userRole = Auth::user()->userRole->name ?? '';

        // Check permissions
        if (!in_array($userRole, ['Supervisor', 'Technical Officer', 'Engineer'])) {
            abort(403, 'You do not have permission to view this request.');
        }

        // Additional check for supervisors
        if ($userRole === 'Supervisor' && $extensionRequest->job->created_by !== Auth::id()) {
            abort(403, 'You can only view extension requests for jobs you created.');
        }

        $extensionRequest->load([
            'job',
            'task',
            'employee.user',
            'requestedBy',
            'reviewedBy'
        ]);

        return view('tasks.extension.show', compact('extensionRequest'));
    }

    /**
     * Approve extension request (FOR ENGINEERS/SUPERVISORS)
     */
    public function approve(Request $request, TaskExtensionRequest $extensionRequest)
    {
        return $this->processRequest($request, $extensionRequest, 'approved');
    }

    /**
     * Reject extension request (FOR ENGINEERS/SUPERVISORS)
     */
    public function reject(Request $request, TaskExtensionRequest $extensionRequest)
    {
        return $this->processRequest($request, $extensionRequest, 'rejected');
    }

    /**
     * Process extension request (approve/reject) - FOR ENGINEERS/SUPERVISORS
     */
    private function processRequest(Request $request, TaskExtensionRequest $extensionRequest, $status)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $job = $extensionRequest->job;
            $task = $extensionRequest->task;
            $employee = $extensionRequest->employee;

            // Check if request can be processed
            if ($extensionRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'This request has already been processed.');
            }

            // Update extension request
            $extensionRequest->update([
                'status' => $status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
                'updated_by' => Auth::id(),
            ]);

            if ($status === 'approved') {
                // Update task deadline in JobEmployee
                JobEmployee::where('task_id', $task->id)
                    ->where('employee_id', $employee->id)
                    ->update([
                        'end_date' => $extensionRequest->requested_end_date,
                        'duration_in_days' => Carbon::parse($extensionRequest->requested_end_date)
                            ->diffInDays(Carbon::parse($extensionRequest->current_end_date)) + 1,
                        'updated_by' => Auth::id(),
                    ]);

                // Update job due date if necessary
                if (!$job->due_date || $extensionRequest->requested_end_date > $job->due_date) {
                    $job->update([
                        'due_date' => $extensionRequest->requested_end_date,
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            // Log extension processing - wrap in try-catch
            try {
                JobActivityLogger::logTaskExtensionProcessed(
                    $job,
                    $task,
                    $employee,
                    $status,
                    $request->review_notes
                );
            } catch (\Exception $logError) {
                Log::warning('Failed to log task extension processing: ' . $logError->getMessage());
            }

            DB::commit();

            $message = $status === 'approved'
                ? 'Task extension approved successfully!'
                : 'Task extension rejected.';

            return redirect()->route('tasks.extension.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process extension request: ' . $e->getMessage(), [
                'extension_request_id' => $extensionRequest->id,
                'status' => $status,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process extension request. Please try again.');
        }
    }

    /**
     * Process extension request with action parameter (ALTERNATIVE METHOD - NOT USED IN YOUR ROUTES)
     */
    public function processTaskExtension(Request $request, TaskExtensionRequest $extensionRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        return $this->processRequest($request, $extensionRequest, $status);
    }

    /**
     * Show employee's own extension requests
     */
    public function myRequests(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            abort(403, 'Employee record not found.');
        }

        $query = TaskExtensionRequest::with([
            'job',
            'task',
            'reviewedBy'
        ])->where('employee_id', $employee->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $extensionRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tasks.extension.my-requests', compact('extensionRequests'));
    }

    /**
     * Get pending extension requests count for dashboard
     */
    public function getPendingCount()
    {
        $companyId = Auth::user()->company_id;
        $userRole = Auth::user()->userRole->name ?? '';

        if (!in_array($userRole, ['Supervisor', 'Technical Officer', 'Engineer'])) {
            return response()->json(['pending_count' => 0]);
        }

        $query = TaskExtensionRequest::pending()->forCompany($companyId);

        // Additional filtering for supervisors
        if ($userRole === 'Supervisor') {
            $query->forSupervisor(Auth::id());
        }

        return response()->json([
            'pending_count' => $query->count()
        ]);
    }

    /**
     * Calculate duration in days
     */
    private function calculateDuration($startDate, $endDate)
    {
        if (!$startDate || $endDate) {
            return null;
        }

        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
    }
}
