<?php

namespace App\Http\Controllers\Task;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\Task;
use App\Models\User;
use App\Models\JobUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JobActivityLogger;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TaskExtensionRequest;
use Illuminate\Support\Facades\Auth;

class TaskExtensionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $userRole = Auth::user()->userRole->name ?? '';

        $query = TaskExtensionRequest::with([
            'job',
            'task',
            'user',
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

    public function create(Task $task)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'User record not found.');
        }

        // Get the job of this task
        $job = Job::findOrFail($task->job_id);

        // Check if user is assigned to this task
        $jobUser = JobUser::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$jobUser) {
            abort(403, 'You are not assigned to this task.');
        }

        // Check if task is not completed
        if ($task->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot request extension for completed task.');
        }

        // Check if there's already a pending request for this task
        $existingRequest = TaskExtensionRequest::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending extension request for this task.');
        }

        return view('tasks.extension.create', compact('task', 'jobUser', 'user', 'job'));
    }

    // FIXED: Change method name to match route
    public function store(Request $request, Task $task)
    {
        // Validate the request
        $request->validate([
            'requested_end_date' => 'required|date|after:today',
            'requested_end_time' => 'nullable|date_format:H:i',
            'reason' => 'required|string|min:10|max:1000',
            'justification' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $job = Job::findOrFail($task->job_id);
            $user = Auth::user();

            if (!$user) {
                throw new \Exception('User record not found.');
            }

            // Get current end date and time
            $jobUser = JobUser::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$jobUser) {
                throw new \Exception('Task assignment not found.');
            }

            // Check for duplicate request again (to prevent race conditions)
            $existingRequest = TaskExtensionRequest::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                DB::rollBack();
                return redirect()->route('tasks.extension.create', $task)
                    ->with('error', 'You already have a pending extension request for this task.');
            }

            $currentEndDate = $jobUser->end_date;
            $currentEndTime = $jobUser->end_time;
            $requestedEndDate = $request->requested_end_date;
            $requestedEndTime = $request->requested_end_time ?: ($currentEndTime ? $currentEndTime->format('H:i') : '23:59');

            // UPDATED: Calculate extension with time precision
            $currentEndDateTime = Carbon::parse($currentEndDate->format('Y-m-d') . ' ' . ($currentEndTime ? $currentEndTime->format('H:i:s') : '23:59:59'));
            $requestedEndDateTime = Carbon::parse($requestedEndDate . ' ' . $requestedEndTime . ':00');

            // Validate that requested end is after current end
            if ($requestedEndDateTime <= $currentEndDateTime) {
                return redirect()->back()
                    ->with('error', 'Requested end date and time must be after the current end date and time.')
                    ->withInput();
            }

            $extensionInRealDays = $currentEndDateTime->floatDiffInRealDays($requestedEndDateTime);
            $extensionDays = floor($extensionInRealDays);
            $extensionHours = ($extensionInRealDays - $extensionDays) * 24;

            // Create extension request
            $extensionRequest = TaskExtensionRequest::create([
                'job_id' => $job->id,
                'task_id' => $task->id,
                'user_id' => $user->id,
                'requested_by' => Auth::id(),
                'current_end_date' => $currentEndDate,
                'current_end_time' => $currentEndTime,
                'requested_end_date' => $requestedEndDate,
                'requested_end_time' => $requestedEndTime,
                'extension_days' => $extensionDays,
                'extension_hours' => $extensionHours,
                'reason' => $request->reason,
                'justification' => $request->justification,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Log extension request - wrap in try-catch to prevent blocking
            try {
                JobActivityLogger::logTaskExtensionRequested(
                    $job,
                    $task,
                    $user,
                    $currentEndDateTime,
                    $requestedEndDateTime,
                    $request->reason
                );
            } catch (\Exception $logError) {
                Log::warning('Failed to log task extension request: ' . $logError->getMessage());
            }

            DB::commit();

            // FIXED: Use proper dashboard route based on user role
            // $userRole = strtolower(str_replace(' ', '', Auth::user()->userRole->name ?? 'employee'));
            // $dashboardRoute = "{$userRole}.dashboard";

            // return redirect()->route($dashboardRoute)
            //     ->with('success', 'Extension request submitted successfully! Your request is pending approval.');

            return redirect()->route('tasks.extension.my-requests')->with('success', 'Extension request submitted successfully! Your request is pending approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Task extension request failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString()
            ]);

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
            'user',  // FIXED: Remove .user relationship chain
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
 * Improved version with better error handling and AJAX support
 */
private function processRequest(Request $request, TaskExtensionRequest $extensionRequest, $status)
{
    $request->validate([
        'review_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $currentUser = Auth::user();
        $job = $extensionRequest->job;
        $task = $extensionRequest->task;
        $user = $extensionRequest->user;

        // Check if request can be processed
        if ($extensionRequest->status !== 'pending') {
            $errorMessage = 'This request has already been processed.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            return redirect()->back()->with('error', $errorMessage);
        }



        // Check company access
        if ($job->company_id !== $currentUser->company_id) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.'
                ], 403);
            }
            abort(403);
        }

        // Additional check for supervisors (they can only process their own jobs)
        if ($job->created_by !== $currentUser->id) {
            $errorMessage = 'You can only process extension requests for jobs you created.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 403);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        // Update extension request
        $extensionRequest->update([
            'status' => $status,
            'reviewed_by' => $currentUser->id,
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
            'updated_by' => $currentUser->id,
        ]);

        if ($status === 'approved') {
            // Find the job user assignment
            $jobUser = JobUser::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$jobUser) {
                throw new \Exception('Task assignment not found for the user.');
            }

            // Prepare job user data with proper time handling
            $jobUserData = [
                'end_date' => $extensionRequest->requested_end_date,
                'end_time' => $extensionRequest->requested_end_time ?: null,
                'updated_by' => $currentUser->id,
            ];

            // Recalculate duration if we have start date/time
            if ($jobUser->start_date && $jobUser->start_time) {
                try {
                    $startDateTime = Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                    $endDateTime = Carbon::parse($extensionRequest->requested_end_date . ' ' . ($extensionRequest->requested_end_time ?: '23:59:59'));
                    $jobUserData['duration'] = $startDateTime->floatDiffInRealDays($endDateTime);
                } catch (\Exception $dateError) {
                    Log::warning('Failed to calculate duration: ' . $dateError->getMessage());
                    // Continue without updating duration
                }
            }

            // Update the job user assignment
            $updateResult = JobUser::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->update($jobUserData);

            if ($updateResult === 0) {
                throw new \Exception('Failed to update task assignment. No matching record found.');
            }

            // Update job due date if necessary
            try {
                $requestedEndDateTime = Carbon::parse($extensionRequest->requested_end_date . ' ' . ($extensionRequest->requested_end_time ?: '23:59:59'));
                $jobDueDateTime = $job->due_date ? Carbon::parse($job->due_date . ' 23:59:59') : null;

                if (!$jobDueDateTime || $requestedEndDateTime->gt($jobDueDateTime)) {
                    $job->update([
                        'due_date' => $extensionRequest->requested_end_date,
                        'updated_by' => $currentUser->id,
                    ]);
                }
            } catch (\Exception $dateError) {
                Log::warning('Failed to update job due date: ' . $dateError->getMessage());
                // Continue without updating job due date
            }
        }

        // Log extension processing
        try {
            JobActivityLogger::logTaskExtensionProcessed(
                $job,
                $task,
                $user,
                $status,
                $request->review_notes
            );
        } catch (\Exception $logError) {
            Log::warning('Failed to log task extension processing: ' . $logError->getMessage());
        }

        DB::commit();

        $message = $status === 'approved'
            ? 'Task extension approved successfully!'
            : 'Task extension rejected successfully.';

        // Handle AJAX requests
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status,
                'redirect_url' => route('tasks.extension.index')
            ]);
        }

        return redirect()->route('tasks.extension.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to process extension request: ' . $e->getMessage(), [
            'extension_request_id' => $extensionRequest->id,
            'status' => $status,
            'user_id' => $currentUser->id ?? 'unknown',
            'trace' => $e->getTraceAsString()
        ]);

        $errorMessage = 'Failed to process extension request. Please try again.';

        // Handle AJAX error responses
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }

        return redirect()->back()
            ->with('error', $errorMessage);
    }
}
    public function myRequests(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'User record not found.');
        }

        $query = TaskExtensionRequest::with([
            'job',
            'task',
            'reviewedBy'
        ])->where('user_id', $user->id);

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
    private function calculateDuration($startDate, $startTime, $endDate, $endTime)
    {
        if (!$startDate || !$endDate) {
            return null;
        }

        $startTimeStr = $startTime ?: '00:00:00';
        $endTimeStr = $endTime ?: '23:59:59';

        $startDateTime = Carbon::parse($startDate . ' ' . $startTimeStr);
        $endDateTime = Carbon::parse($endDate . ' ' . $endTimeStr);

        return $startDateTime->floatDiffInRealDays($endDateTime);
    }
}
