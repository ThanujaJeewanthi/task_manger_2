<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobActivityLog;
use App\Services\JobActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;

class JobHistoryController extends Controller
{
    /**
     * Display job history timeline with filters and pagination.
     */
   public function index(Job $job, Request $request)
    {
        try {
            // Check company access
            if ($job->company_id !== Auth::user()->company_id) {
                abort(403, 'Access denied to this job.');
            }

            // Validate and get filter parameters
            $filters = $request->only(['category', 'type', 'user_id', 'date_from', 'date_to', 'major_only']);

            // Build the main query
            $query = JobActivityLog::where('job_id', $job->id)
                ->with(['user', 'affectedUser'])
                ->where('active', true) // Only show active logs
                ->orderBy('created_at', 'desc');

            // Apply filters
            $this->applyFilters($query, $filters);

            // Paginate results
            $activities = $query->paginate(20)->appends($request->query());

            // Get activity statistics
            $stats = $this->getJobActivityStats($job->id);

            // Get unique users for filter dropdown
            $users = JobActivityLog::where('job_id', $job->id)
                ->with('user:id,name')
                ->select('user_id')
                ->distinct()
                ->get()
                ->pluck('user')
                ->filter()
                ->unique('id')
                ->sortBy('name');

            // Get available categories and types for filters
            $categories = JobActivityLog::where('job_id', $job->id)
                ->distinct('activity_category')
                ->orderBy('activity_category')
                ->pluck('activity_category');

            $types = JobActivityLog::where('job_id', $job->id)
                ->distinct('activity_type')
                ->orderBy('activity_type')
                ->pluck('activity_type');

            // Load job relationships for display
            $job->load(['jobType', 'client', 'equipment', 'company', 'assignedUser']);

            return view('jobs.history.index', compact(
                'job', 'activities', 'stats', 'users', 'categories', 'types', 'filters'
            ));

        } catch (\Exception $e) {
            Log::error('Job history index error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id(),
                'filters' => $filters ?? []
            ]);

            return redirect()->route('jobs.show', $job)
                ->with('error', 'Unable to load job history. Please try again.');
        }
    }

    /**
     * Show detailed activity information.
     */
   public function show(Job $job, JobActivityLog $activity)
    {
        try {
            // Check company access and activity belongs to job
            if ($job->company_id !== Auth::user()->company_id || $activity->job_id !== $job->id) {
                abort(403, 'Access denied to this activity.');
            }

            // Load all relationships
            $activity->load(['user', 'affectedUser', 'job.jobType', 'job.client', 'job.equipment']);

            // Get previous and next activities for navigation
            $previousActivity = JobActivityLog::where('job_id', $job->id)
                ->where('created_at', '<', $activity->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            $nextActivity = JobActivityLog::where('job_id', $job->id)
                ->where('created_at', '>', $activity->created_at)
                ->orderBy('created_at', 'asc')
                ->first();

            // Get related activities (same type or affecting same user)
            $relatedActivities = JobActivityLog::where('job_id', $job->id)
                ->where('id', '!=', $activity->id)
                ->where(function($query) use ($activity) {
                    $query->where('activity_type', $activity->activity_type)
                          ->orWhere('affected_user_id', $activity->affected_user_id)
                          ->orWhere('related_model_type', $activity->related_model_type);
                })
                ->with(['user', 'affectedUser'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return view('jobs.history.show', compact(
                'job', 'activity', 'previousActivity', 'nextActivity', 'relatedActivities'
            ));

        } catch (\Exception $e) {
            Log::error('Job history show error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'activity_id' => $activity->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('jobs.history.index', $job)
                ->with('error', 'Unable to load activity details.');
        }
    }
    /**
     * Export job history as PDF.
     */
     public function exportPdf(Job $job, Request $request)
    {
        try {
            // Check company access
            if ($job->company_id !== Auth::user()->company_id) {
                abort(403, 'Access denied to this job.');
            }

            // Validate and get filter parameters
            $filters = $request->only(['category', 'type', 'user_id', 'date_from', 'date_to', 'major_only']);

            // Build the main query
            $query = JobActivityLog::where('job_id', $job->id)
                ->with(['user', 'affectedUser'])
                ->where('active', true) // Only show active logs
                ->orderBy('created_at', 'desc');

            // Apply filters
            $this->applyFilters($query, $filters);

            // Get all activities for export
            $activities = $query->get();

            // Generate PDF
            $pdf = Pdf::loadView('jobs.history.pdf', [
                'job' => $job,
                'activities' => $activities,
                'filters' => $filters,
                'stats' => $this->getJobActivityStats($job->id)
            ]);

            return $pdf->download("job_{$job->id}_history.pdf");

        } catch (\Exception $e) {
            Log::error('Job history export PDF error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id(),
                'filters' => $filters ?? []
            ]);

            return redirect()->route('jobs.history.index', $job)
                ->with('error', 'Unable to export PDF. Please try again.');
        }


    }


    /**
     * Get activity data for timeline visualization (AJAX).
     */
  public function getTimelineData(Job $job, Request $request)
    {
        try {
            // Check company access
            if ($job->company_id !== Auth::user()->company_id) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $activities = JobActivityLog::where('job_id', $job->id)
                ->with(['user', 'affectedUser'])
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $timelineData = $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'date' => $activity->created_at->format('M d, Y g:i A'),
                    'type' => $activity->activity_type,
                    'category' => $activity->activity_category,
                    'description' => $activity->description,
                    'user' => $activity->user ? $activity->user->name : 'System',
                    'is_major' => $activity->is_major_activity,
                    'priority' => $activity->priority_level,
                ];
            });

            return response()->json($timelineData);

        } catch (\Exception $e) {
            Log::error('Timeline data error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id()
            ]);

            return response()->json(['error' => 'Unable to load timeline data'], 500);
        }
    }

    /**
     * Get activity statistics summary (AJAX).
     */
    public function getActivityStats(Job $job)
    {
        try {
            if ($job->company_id !== Auth::user()->company_id) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $stats = $this->getJobActivityStats($job->id);
            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Activity stats error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id()
            ]);

            return response()->json(['error' => 'Unable to load statistics'], 500);
        }
    }

    /**
     * Apply filters to the activity query.
     */
   private function applyFilters($query, $filters)
    {
        if (!empty($filters['category'])) {
            $query->where('activity_category', $filters['category']);
        }

        if (!empty($filters['type'])) {
            $query->where('activity_type', $filters['type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['major_only'])) {
            $query->where('is_major_activity', true);
        }
    }


    /**
     * Get comprehensive job activity statistics.
     */
    private function getJobActivityStats($jobId)
    {
        return JobActivityLogger::getJobActivityStats($jobId);
    }


}
