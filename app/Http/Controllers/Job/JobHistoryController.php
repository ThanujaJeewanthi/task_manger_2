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
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Shared\Converter;
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
            // Check if PDF package is available
            if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                return response()->json([
                    'error' => 'PDF export functionality is not available. Please contact administrator.'
                ], 500);
            }

            // Check company access
            if ($job->company_id !== Auth::user()->company_id) {
                abort(403, 'Access denied to this job.');
            }

            // Get filters and validate dates
            $filters = $request->only(['category', 'type', 'user_id', 'date_from', 'date_to', 'major_only', 'activity_id']);

            // Validate date range if provided
            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                try {
                    $startDate = Carbon::parse($filters['date_from']);
                    $endDate = Carbon::parse($filters['date_to']);

                    if ($startDate->gt($endDate)) {
                        return redirect()->back()->with('error', 'Start date cannot be after end date.');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Invalid date format.');
                }
            }

            // Build query for activities
            $query = JobActivityLog::where('job_id', $job->id)
                ->with(['user', 'affectedUser', 'job.jobType', 'job.client', 'job.equipment', 'job.company'])
                ->where('active', true)
                ->orderBy('created_at', 'desc');

            // If specific activity requested, filter to that
            if (!empty($filters['activity_id'])) {
                $query->where('id', $filters['activity_id']);
            } else {
                // Apply other filters
                $this->applyFilters($query, $filters);
            }

            $activities = $query->get();

            // Get statistics
            $stats = $this->getJobActivityStats($job->id);

            // Prepare data for PDF
            $data = [
                'job' => $job->load(['jobType', 'client', 'equipment', 'company', 'assignedUser']),
                'activities' => $activities,
                'stats' => $stats,
                'filters' => $filters,
                'generated_at' => now(),
                'generated_by' => Auth::user(),
                'company_info' => [
                    'name' => $job->company->name ?? 'N/A',
                    'address' => $job->company->address ?? '',
                    'phone' => $job->company->phone ?? '',
                    'email' => $job->company->email ?? '',
                ]
            ];

            // Configure PDF settings
            $pdf = Pdf::loadView('jobs.history.pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'chroot' => public_path(),
                ]);

            $filename = "job-{$job->id}-history-" . now()->format('Y-m-d-H-i-s') . ".pdf";

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF export error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id(),
                'filters' => $filters ?? []
            ]);

            return redirect()->back()->with('error', 'Unable to generate PDF. Please try again later.');
        }
    }

    /**
     * Export job history as Word document.
     */
    public function exportWord(Job $job, Request $request)
    {
        try {
            // Check if PhpWord package is available
            if (!class_exists('\PhpOffice\PhpWord\PhpWord')) {
                return response()->json([
                    'error' => 'Word export functionality is not available. Please contact administrator.'
                ], 500);
            }

            // Check company access
            if ($job->company_id !== Auth::user()->company_id) {
                abort(403, 'Access denied to this job.');
            }

            $filters = $request->only(['category', 'type', 'user_id', 'date_from', 'date_to', 'major_only', 'activity_id']);

            // Build query for activities (same as PDF)
            $query = JobActivityLog::where('job_id', $job->id)
                ->with(['user', 'affectedUser', 'job.jobType', 'job.client', 'job.equipment', 'job.company'])
                ->where('active', true)
                ->orderBy('created_at', 'desc');

            // Apply filters
            if (!empty($filters['activity_id'])) {
                $query->where('id', $filters['activity_id']);
            } else {
                $this->applyFilters($query, $filters);
            }

            $activities = $query->get();
            $stats = $this->getJobActivityStats($job->id);

            // Create Word document
            $phpWord = new PhpWord();

            // Set document properties
            $properties = $phpWord->getDocInfo();
            $properties->setCreator(Auth::user()->name);
            $properties->setCompany($job->company->name ?? 'Task Management System');
            $properties->setTitle("Job History Report - Job #{$job->id}");
            $properties->setDescription("Detailed activity log for job #{$job->id}");
            $properties->setSubject("Job History Report");

            // Add document section
            $section = $phpWord->addSection([
                'marginLeft' => Converter::cmToTwip(2.5),
                'marginRight' => Converter::cmToTwip(2.5),
                'marginTop' => Converter::cmToTwip(2),
                'marginBottom' => Converter::cmToTwip(2),
            ]);

            // Document header
            $section->addTitle("Job History Report", 1);
            $section->addTitle("Job #{$job->id}: " . $job->description, 2);
            $section->addTextBreak(1);

            // Job information table
            $jobInfoTable = $section->addTable([
                'borderSize' => 3,
                'borderColor' => 'cccccc',
                'cellMargin' => 80,
                'width' => 100 * 50,
                'unit' => 'pct'
            ]);

            $this->addJobInfoToWordTable($jobInfoTable, $job);

            $section->addTextBreak(2);

            // Statistics section
            $section->addTitle("Activity Statistics", 2);
            $statsTable = $section->addTable([
                'borderSize' => 3,
                'borderColor' => 'cccccc',
                'cellMargin' => 80,
            ]);

            $this->addStatsToWordTable($statsTable, $stats);

            $section->addTextBreak(2);

            // Applied filters (if any)
            if (!empty(array_filter($filters))) {
                $section->addTitle("Applied Filters", 2);
                $this->addFiltersToWord($section, $filters);
                $section->addTextBreak(1);
            }

            // Activities timeline
            $section->addTitle("Activity Timeline", 2);
            $section->addTextBreak(1);

            if ($activities->count() > 0) {
                foreach ($activities as $index => $activity) {
                    $this->addActivityToWord($section, $activity, $index + 1);

                    // Add page break every 8 activities to prevent overcrowding
                    if (($index + 1) % 8 === 0 && $index + 1 < $activities->count()) {
                        $section->addPageBreak();
                    }
                }
            } else {
                $section->addText("No activities found matching the specified criteria.",
                    ['italic' => true, 'color' => '666666']);
            }

            // Footer
            $section->addTextBreak(2);
            $section->addText("Report generated on " . now()->format('M d, Y H:i:s') . " by " . Auth::user()->name,
                ['size' => 9, 'italic' => true, 'color' => '666666']);

            // Save and download
            $filename = "job-{$job->id}-history-" . now()->format('Y-m-d-H-i-s') . ".docx";
            $tempFile = tempnam(sys_get_temp_dir(), 'job_history');

            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Word export error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_id' => Auth::id(),
                'filters' => $filters ?? []
            ]);

            return redirect()->back()->with('error', 'Unable to generate Word document. Please try again later.');
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
                ->orderBy('created_at', 'asc')
                ->get();

            $timelineData = $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'date' => $activity->created_at->toISOString(),
                    'title' => ucfirst(str_replace('_', ' ', $activity->activity_type)),
                    'description' => $activity->description,
                    'category' => $activity->activity_category,
                    'user' => $activity->user->name ?? 'System',
                    'user_role' => $activity->user_role,
                    'is_major' => $activity->is_major_activity,
                    'priority' => $activity->priority_level,
                    'icon' => $activity->activity_icon,
                    'badge_class' => $activity->priority_badge,
                    'url' => route('jobs.history.show', [$job, $activity]),
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
            $query->byCategory($filters['category']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['user_id']) && is_numeric($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            try {
                $query->inDateRange($filters['date_from'], $filters['date_to']);
            } catch (\Exception $e) {
                Log::warning('Invalid date range in filters', $filters);
            }
        }

        if (!empty($filters['major_only'])) {
            $query->majorActivities();
        }

        return $query;
    }

    /**
     * Get comprehensive job activity statistics.
     */
    private function getJobActivityStats($jobId)
    {
        try {
            $totalActivities = JobActivityLog::where('job_id', $jobId)->where('active', true)->count();

            return [
                'total_activities' => $totalActivities,
                'major_activities' => JobActivityLog::where('job_id', $jobId)
                    ->where('is_major_activity', true)
                    ->where('active', true)
                    ->count(),
                'users_involved' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->whereNotNull('user_id')
                    ->distinct('user_id')
                    ->count('user_id'),
                'last_activity' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->latest()
                    ->first()?->created_at,
                'first_activity' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->oldest()
                    ->first()?->created_at,
                'activity_by_category' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->selectRaw('activity_category, COUNT(*) as count')
                    ->groupBy('activity_category')
                    ->pluck('count', 'activity_category'),
                'activity_by_priority' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->selectRaw('priority_level, COUNT(*) as count')
                    ->groupBy('priority_level')
                    ->pluck('count', 'priority_level'),
                'recent_activities' => JobActivityLog::where('job_id', $jobId)
                    ->where('active', true)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Stats calculation error: ' . $e->getMessage(), ['job_id' => $jobId]);
            return [
                'total_activities' => 0,
                'major_activities' => 0,
                'users_involved' => 0,
                'last_activity' => null,
                'first_activity' => null,
                'activity_by_category' => collect(),
                'activity_by_priority' => collect(),
                'recent_activities' => 0,
            ];
        }
    }

    /**
     * Add job information to Word document table.
     */
    private function addJobInfoToWordTable($table, $job)
    {
        $jobInfo = [
            'Job Type' => $job->jobType->name ?? 'N/A',
            'Client' => $job->client->name ?? 'N/A',
            'Equipment' => $job->equipment->name ?? 'N/A',
            'Status' => ucfirst($job->status),
            'Priority' => ucfirst($job->priority),
            'Assigned To' => $job->assignedUser->name ?? 'Unassigned',
            'Created Date' => $job->created_at->format('M d, Y H:i:s'),
            'Company' => $job->company->name ?? 'N/A',
        ];

        foreach ($jobInfo as $label => $value) {
            $row = $table->addRow();
            $row->addCell(3000)->addText($label . ':', ['bold' => true, 'size' => 10]);
            $row->addCell(6000)->addText($value, ['size' => 10]);
        }
    }

    /**
     * Add statistics to Word document table.
     */
    private function addStatsToWordTable($table, $stats)
    {
        $statsInfo = [
            'Total Activities' => $stats['total_activities'],
            'Major Activities' => $stats['major_activities'],
            'Users Involved' => $stats['users_involved'],
            'Last Activity' => $stats['last_activity'] ? $stats['last_activity']->format('M d, Y H:i:s') : 'N/A',
            'Recent Activities (7 days)' => $stats['recent_activities'] ?? 0,
        ];

        foreach ($statsInfo as $label => $value) {
            $row = $table->addRow();
            $row->addCell(4000)->addText($label . ':', ['bold' => true, 'size' => 10]);
            $row->addCell(5000)->addText($value, ['size' => 10]);
        }
    }

    /**
     * Add filters information to Word document.
     */
    private function addFiltersToWord($section, $filters)
    {
        $appliedFilters = [];

        if (!empty($filters['category'])) {
            $appliedFilters[] = "Category: " . ucfirst(str_replace('_', ' ', $filters['category']));
        }

        if (!empty($filters['type'])) {
            $appliedFilters[] = "Type: " . ucfirst(str_replace('_', ' ', $filters['type']));
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $appliedFilters[] = "Date Range: {$filters['date_from']} to {$filters['date_to']}";
        }

        if (!empty($filters['major_only'])) {
            $appliedFilters[] = "Show Only: Major Activities";
        }

        foreach ($appliedFilters as $filter) {
            $section->addText("• " . $filter, ['size' => 10]);
        }
    }

    /**
     * Add individual activity to Word document.
     */
    private function addActivityToWord($section, $activity, $activityNumber)
    {
        // Activity header
        $activityTitle = $activity->is_major_activity ? "★ " : "";
        $activityTitle .= "#{$activityNumber} - " . ucfirst(str_replace('_', ' ', $activity->activity_type));
        $activityTitle .= " (" . ucfirst($activity->activity_category) . ")";

        $section->addTitle($activityTitle, 3);

        // Activity details table
        $activityTable = $section->addTable([
            'borderSize' => 1,
            'borderColor' => 'cccccc',
            'cellMargin' => 60,
        ]);

        // Basic information
        $activityTable->addRow();
        $activityTable->addCell(2500)->addText('Date & Time:', ['bold' => true, 'size' => 9]);
        $activityTable->addCell(6500)->addText($activity->created_at->format('M d, Y H:i:s'), ['size' => 9]);

        $activityTable->addRow();
        $activityTable->addCell(2500)->addText('Performed By:', ['bold' => true, 'size' => 9]);
        $activityTable->addCell(6500)->addText(
            ($activity->user->name ?? 'System') .
            ($activity->user_role ? " ({$activity->user_role})" : ''),
            ['size' => 9]
        );

        if ($activity->affected_user_id) {
            $activityTable->addRow();
            $activityTable->addCell(2500)->addText('Affected User:', ['bold' => true, 'size' => 9]);
            $activityTable->addCell(6500)->addText($activity->affectedUser->name ?? 'Unknown', ['size' => 9]);
        }

        $activityTable->addRow();
        $activityTable->addCell(2500)->addText('Description:', ['bold' => true, 'size' => 9]);
        $activityTable->addCell(6500)->addText($activity->description, ['size' => 9]);

        $activityTable->addRow();
        $activityTable->addCell(2500)->addText('Priority:', ['bold' => true, 'size' => 9]);
        $activityTable->addCell(6500)->addText(ucfirst($activity->priority_level), ['size' => 9]);

        // Old and new values
        if ($activity->old_values && !empty($activity->old_values)) {
            $activityTable->addRow();
            $activityTable->addCell(2500)->addText('Previous Values:', ['bold' => true, 'size' => 9]);
            $activityTable->addCell(6500)->addText($this->formatValuesForWord($activity->old_values), ['size' => 9]);
        }

        if ($activity->new_values && !empty($activity->new_values)) {
            $activityTable->addRow();
            $activityTable->addCell(2500)->addText('New Values:', ['bold' => true, 'size' => 9]);
            $activityTable->addCell(6500)->addText($this->formatValuesForWord($activity->new_values), ['size' => 9]);
        }

        if ($activity->metadata && !empty($activity->metadata)) {
            $activityTable->addRow();
            $activityTable->addCell(2500)->addText('Additional Info:', ['bold' => true, 'size' => 9]);
            $activityTable->addCell(6500)->addText($this->formatValuesForWord($activity->metadata), ['size' => 9]);
        }

        if ($activity->related_entity_name) {
            $activityTable->addRow();
            $activityTable->addCell(2500)->addText('Related Entity:', ['bold' => true, 'size' => 9]);
            $activityTable->addCell(6500)->addText($activity->related_entity_name, ['size' => 9]);
        }

        $section->addTextBreak(1);
    }

    /**
     * Format array values for Word document display.
     */
    private function formatValuesForWord($values)
    {
        if (empty($values)) {
            return 'None';
        }

        $formatted = [];
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'Yes' : 'No';
            } elseif (is_null($value)) {
                $value = 'N/A';
            }

            $formattedKey = ucfirst(str_replace('_', ' ', $key));
            $formatted[] = "{$formattedKey}: {$value}";
        }

        return implode(' | ', $formatted);
    }


   
}
