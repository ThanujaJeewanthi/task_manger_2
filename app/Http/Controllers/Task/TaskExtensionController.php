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
            'user.user',
            'requestedBy',
            'reviewedBy'
        ]);

        return view('tasks.extension.show', compact('extensionRequest'));
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
        $user = $extensionRequest->user;

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
            // UPDATED: Update task deadline in JobUser with time components
            JobUser::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->update([
                    'end_date' => $extensionRequest->requested_end_date,
                    'end_time' => $extensionRequest->requested_end_time, // ADDED
                    // UPDATED: Recalculate duration with new end time
                    'duration' => function($query) use ($extensionRequest) {
                        $jobUser = JobUser::where('task_id', $extensionRequest->task_id)
                            ->where('user_id', $extensionRequest->user_id)
                            ->first();

                        if ($jobUser && $jobUser->start_date && $jobUser->start_time) {
                            $startDateTime = Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                            $endDateTime = Carbon::parse($extensionRequest->requested_end_date . ' ' . ($extensionRequest->requested_end_time ?: '23:59:59'));
                            return $startDateTime->floatDiffInRealDays($endDateTime);
                        }
                        return null;
                    },
                    'updated_by' => Auth::id(),
                ]);

            // Update job due date if necessary (considering time)
            $requestedEndDateTime = Carbon::parse($extensionRequest->requested_end_date . ' ' . ($extensionRequest->requested_end_time ?: '23:59:59'));
            $jobDueDateTime = $job->due_date ? Carbon::parse($job->due_date . ' 23:59:59') : null;

            if (!$jobDueDateTime || $requestedEndDateTime->gt($jobDueDateTime)) {
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


public function create(Task $task)
{
    $user = Auth::user();

    // Check if user is assigned to this task
    $jobUser = JobUser::where('user_id', $user->id)
        ->where('task_id', $task->id)
        ->first();

    if (!$jobUser) {
        return redirect()->back()->with('error', 'You are not assigned to this task.');
    }

    $job = Job::findOrFail($task->job_id);

    // Check company access
    if ($job->company_id !== $user->company_id) {
        abort(403, 'Unauthorized access to this task.');
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

/**
 * Store task extension request
 */
public function requestTaskExtension(Request $request, Task $task)
{
    // Validate the request
    $request->validate([
        'requested_end_date' => 'required|date|after:today',
        'requested_end_time' => 'nullable|date_format:H:i',
        'reason' => 'required|string|max:1000',
        'justification' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $job = Job::findOrFail($task->job_id);
        $user = Auth::user();

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

        // Calculate extension with time precision
        $currentEndDateTime = Carbon::parse($currentEndDate->format('Y-m-d') . ' ' . ($currentEndTime ? $currentEndTime->format('H:i:s') : '23:59:59'));
        $requestedEndDateTime = Carbon::parse($requestedEndDate . ' ' . $requestedEndTime . ':00');

        $extensionDays = $currentEndDateTime->diffInRealDays($requestedEndDateTime);

        // Create extension request
        $extensionRequest = TaskExtensionRequest::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'current_end_date' => $currentEndDate,
            'current_end_time' => $currentEndTime,
            'requested_end_date' => $requestedEndDate,
            'requested_end_time' => $requestedEndTime,
            'extension_days' => $extensionDays,
            'reason' => $request->reason,
            'justification' => $request->justification,
            'status' => 'pending',
            'requested_by' => $user->id,
            'created_by' => $user->id,
        ]);

        // Log the extension request
        JobActivityLogger::logTaskExtensionRequested($job, $task, $user, $extensionRequest);

        DB::commit();

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Task extension request submitted successfully. It will be reviewed by your supervisor.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating task extension request: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to submit extension request. Please try again.');
    }
}

/**
 * Show user's own extension requests
 */
public function myRequests()
{
    $user = Auth::user();

    $extensionRequests = TaskExtensionRequest::where('user_id', $user->id)
        ->with(['task.job', 'task.job.jobType', 'reviewer'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('tasks.extension.my-requests', compact('extensionRequests'));
}

/**
 * Show extension requests for approval (for managers/supervisors)
 */
public function index(Request $request)
{
    $user = Auth::user();

    // Base query for extension requests that need approval
    $query = TaskExtensionRequest::with(['task.job', 'task.job.jobType', 'requestedByUser'])
        ->whereHas('task.job', function($q) use ($user) {
            $q->where('company_id', $user->company_id);
        });

    // Filter by status
    $status = $request->get('status', 'pending');
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Filter by job type if specified
    if ($request->has('job_type_id') && $request->job_type_id) {
        $query->whereHas('task.job', function($q) use ($request) {
            $q->where('job_type_id', $request->job_type_id);
        });
    }

    $extensionRequests = $query->orderBy('created_at', 'desc')->paginate(15);

    // Get job types for filter dropdown
    $jobTypes = \App\Models\JobType::where('active', true)
        ->orderBy('name')
        ->get();

    return view('tasks.extension.index', compact('extensionRequests', 'jobTypes', 'status'));
}

/**
 * Approve extension request
 */
public function approve(Request $request, TaskExtensionRequest $extensionRequest)
{
    $request->validate([
        'approved_end_date' => 'required|date',
        'approved_end_time' => 'nullable|date_format:H:i',
        'reviewer_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $user = Auth::user();
        $task = $extensionRequest->task;
        $job = $task->job;

        // Check if request is still pending
        if ($extensionRequest->status !== 'pending') {
            throw new \Exception('This extension request has already been processed.');
        }

        // Update extension request
        $extensionRequest->update([
            'status' => 'approved',
            'approved_end_date' => $request->approved_end_date,
            'approved_end_time' => $request->approved_end_time,
            'reviewer_notes' => $request->reviewer_notes,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Update the job user's end date and time
        $jobUser = JobUser::where('task_id', $task->id)
            ->where('user_id', $extensionRequest->user_id)
            ->first();

        if ($jobUser) {
            $jobUser->update([
                'end_date' => $request->approved_end_date,
                'end_time' => $request->approved_end_time,
                'updated_by' => $user->id,
            ]);
        }

        // Log the approval
        JobActivityLogger::logTaskExtensionApproved($job, $task, $extensionRequest, $user);

        DB::commit();

        return redirect()->back()
            ->with('success', 'Task extension approved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error approving task extension: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to approve extension request. Please try again.');
    }
}

/**
 * Reject extension request
 */
public function reject(Request $request, TaskExtensionRequest $extensionRequest)
{
    $request->validate([
        'reviewer_notes' => 'required|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        $user = Auth::user();
        $task = $extensionRequest->task;
        $job = $task->job;

        // Check if request is still pending
        if ($extensionRequest->status !== 'pending') {
            throw new \Exception('This extension request has already been processed.');
        }

        // Update extension request
        $extensionRequest->update([
            'status' => 'rejected',
            'reviewer_notes' => $request->reviewer_notes,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Log the rejection
        JobActivityLogger::logTaskExtensionRejected($job, $task, $extensionRequest, $user);

        DB::commit();

        return redirect()->back()
            ->with('success', 'Task extension request rejected.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error rejecting task extension: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to reject extension request. Please try again.');
    }
}

/**
 * Get pending extension count for API
 */
public function getPendingCount()
{
    $user = Auth::user();

    $count = TaskExtensionRequest::where('status', 'pending')
        ->whereHas('task.job', function($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })
        ->count();

    return response()->json(['count' => $count]);
}

}
