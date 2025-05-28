<?php

namespace App\Http\Controllers\Job;

use App\Models\Job;
use App\Models\Item;
use App\Models\Task;
use App\Models\Client;
use App\Models\JobType;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\JobOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $jobs = Job::with(['jobType', 'client', 'equipment'])
            ->where('company_id', $companyId)
            ->where('active', true)
            ->paginate(10);

        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $jobTypes = JobType::with(['jobOptions'])->where('active', true)->get();
        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();
        $employees = Employee::where('company_id', $companyId)->where('active', true)->get();

        return view('jobs.create', compact('jobTypes', 'clients', 'equipments', 'employees'));
    }

    public function getJobTypeOptions($jobTypeId)
    {
        $jobType = JobType::with(['jobOptions'])->find($jobTypeId);

        if (!$jobType) {
            return response()->json(['error' => 'Job type not found'], 404);
        }

        return response()->json([
            'job_options' => $jobType->jobOptions->map(function($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'description' => $option->description,
                    'option_type' => $option->option_type,
                    'required' => $option->required,
                    'options_json' => $option->options_json,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        // Get job type and its options for validation
        $jobType = null;
        if ($request->has('job_type_id') && $request->job_type_id) {
            $jobType = JobType::with('jobOptions')->find($request->job_type_id);
        }

        // Build validation rules dynamically
        $rules = $this->getValidationRules($jobType);
        $request->validate($rules);

        // Prepare job data
        $jobData = $this->prepareJobData($request);

        // Handle job option values
        $jobOptionValues = $this->processJobOptionValues($request, $jobType);
        if (!empty($jobOptionValues)) {
            $jobData['job_option_values'] = $jobOptionValues;
        }

        // Create the job
        $job = Job::create($jobData);

        return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
    }

    public function show(Job $job)
    {
        // Check authorization
        $this->authorizeJobAccess($job);

        $job->load(['jobType.jobOptions', 'client', 'equipment', 'jobEmployees.employee', 'jobEmployees.task']);
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('active', true)->get();
        $tasks = Task::where('job_id', $job->id)->where('active', true)->with('jobEmployees.employee')->get();

        // Decode job option values for display
        $jobOptionValues = $job->job_option_values ? json_decode($job->job_option_values, true) : [];

        return view('jobs.show', compact('job', 'employees', 'tasks', 'jobOptionValues'));
    }

    public function edit(Job $job)
    {
        $this->authorizeJobAccess($job);

        $companyId = Auth::user()->company_id;
        $jobTypes = JobType::with('jobOptions')->where('active', true)->get();
        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        // Decode job option values for editing
        $jobOptionValues = $job->job_option_values ? json_decode($job->job_option_values, true) : [];

        return view('jobs.edit', compact('job', 'jobTypes', 'clients', 'equipments', 'jobOptionValues'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorizeJobAccess($job);

        // Get job type and its options for validation
        $jobType = null;
        if ($request->has('job_type_id') && $request->job_type_id) {
            $jobType = JobType::with('jobOptions')->find($request->job_type_id);
        }

        // Build validation rules (excluding unique job_number for current job)
        $rules = $this->getValidationRules($jobType, $job->id);
        $request->validate($rules);

        // Prepare job data
        $jobData = $this->prepareJobData($request, true);

        // Handle job option values
        $jobOptionValues = $this->processJobOptionValues($request, $jobType);
        if (!empty($jobOptionValues)) {
            $jobData['job_option_values'] = $jobOptionValues;
        }

        // Handle photo updates
        if ($request->hasFile('photos')) {
            // Delete old photos
            if ($job->photos) {
                $oldPhotos = json_decode($job->photos, true);
                foreach ($oldPhotos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $photos[] = $photo->store('job_photos', 'public');
            }
            $jobData['photos'] = json_encode($photos);
        }

        $job->update($jobData);

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->authorizeJobAccess($job);

        $job->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Get validation rules based on job type and its options
     */
    private function getValidationRules($jobType = null, $jobId = null)
    {
        $rules = [
            'job_number' => 'required|string|max:50|unique:jobs' . ($jobId ? ',job_number,' . $jobId : ''),
            'job_type_id' => 'required|exists:job_types,id',
            'client_id' => 'nullable|exists:clients,id',
            'equipment_id' => 'nullable|exists:equipments,id',
            'description' => 'nullable|string',
            'references' => 'nullable|string',
            'priority' => 'required|in:1,2,3,4',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Add update-specific rules
        if ($jobId) {
            $rules['status'] = 'required|in:draft,pending,in_progress,on_hold,completed,cancelled';
            $rules['completed_date'] = 'nullable|date';
        }

        // Add job option validation rules
        if ($jobType && $jobType->jobOptions) {
            foreach ($jobType->jobOptions as $option) {
                $fieldName = 'job_option_' . $option->id;
                $rules[$fieldName] = $this->getOptionValidationRule($option);
            }
        }

        return $rules;
    }

    /**
     * Get validation rule for a specific job option
     */
    private function getOptionValidationRule($option)
    {
        $baseRule = $option->required ? 'required' : 'nullable';

        switch ($option->option_type) {
            case 'number':
                return $baseRule . '|numeric';
            case 'date':
                return $baseRule . '|date';
            case 'file':
                return $baseRule . '|file|max:2048';
            case 'checkbox':
                return 'nullable|boolean';
            case 'select':
                // For equipment and client, validate against their respective tables
                if ($this->isEquipmentOption($option)) {
                    return $baseRule . '|exists:equipments,id';
                } elseif ($this->isClientOption($option)) {
                    return $baseRule . '|exists:clients,id';
                } else {
                    return $baseRule . '|string|max:255';
                }
            default:
                return $baseRule . '|string|max:255';
        }
    }

    /**
     * Check if option is equipment option (you can implement your own logic)
     */
    private function isEquipmentOption($option)
    {
        return strtolower($option->name) === 'equipment' || $option->id == 1;
    }

    /**
     * Check if option is client option (you can implement your own logic)
     */
    private function isClientOption($option)
    {
        return strtolower($option->name) === 'client' || $option->id == 2;
    }

    /**
     * Prepare job data for creation/update
     */
    private function prepareJobData(Request $request, $isUpdate = false)
    {
        $data = [
            'job_number' => $request->job_number,
            'job_type_id' => $request->job_type_id,
            'client_id' => $request->client_id,
            'equipment_id' => $request->equipment_id,
            'description' => $request->description,
            'references' => $request->references,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'active' => $request->has('is_active'),
        ];

        if ($isUpdate) {
            $data['status'] = $request->status;
            $data['completed_date'] = $request->completed_date;
            $data['updated_by'] = Auth::id();
        } else {
            $data['company_id'] = Auth::user()->company_id;
            $data['status'] = 'draft';
            $data['created_by'] = Auth::id();
        }

        // Handle photo uploads for creation
        if (!$isUpdate && $request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $photos[] = $photo->store('job_photos', 'public');
            }
            $data['photos'] = json_encode($photos);
        }

        return $data;
    }

    /**
     * Process job option values from request
     */
    private function processJobOptionValues(Request $request, $jobType = null)
    {
        if (!$jobType || !$jobType->jobOptions) {
            return [];
        }

        $jobOptionValues = [];

        foreach ($jobType->jobOptions as $option) {
            $fieldName = 'job_option_' . $option->id;

            if ($request->has($fieldName)) {
                $value = $request->input($fieldName);

                // Handle special cases for equipment and client options
                if ($this->isEquipmentOption($option)) {
                    // Don't store in job_option_values, it's already in equipment_id
                    continue;
                } elseif ($this->isClientOption($option)) {
                    // Don't store in job_option_values, it's already in client_id
                    continue;
                }

                // Handle file uploads
                if ($option->option_type === 'file' && $request->hasFile($fieldName)) {
                    $jobOptionValues[$option->id] = $request->file($fieldName)->store('job_option_files', 'public');
                } elseif ($option->option_type === 'checkbox') {
                    $jobOptionValues[$option->id] = $request->has($fieldName) ? true : false;
                } else {
                    $jobOptionValues[$option->id] = $value;
                }
            } elseif ($option->option_type === 'checkbox') {
                // Ensure checkbox values are properly handled when unchecked
                $jobOptionValues[$option->id] = false;
            }
        }

        return $jobOptionValues;
    }

    /**
     * Authorize job access for current user's company
     */
    private function authorizeJobAccess(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }

    /**
     * Get job option value by option ID
     */
    public function getJobOptionValue(Job $job, $optionId)
    {
        if (!$job->job_option_values) {
            return null;
        }

        $values = json_decode($job->job_option_values, true);
        return $values[$optionId] ?? null;
    }

    /**
     * Get all job option values with option details
     */
    public function getJobOptionValuesWithDetails(Job $job)
    {
        if (!$job->job_option_values || !$job->jobType) {
            return collect();
        }

        $values = json_decode($job->job_option_values, true);
        $job->load('jobType.jobOptions');

        return $job->jobType->jobOptions->map(function ($option) use ($values) {
            return [
                'option' => $option,
                'value' => $values[$option->id] ?? null,
                'formatted_value' => $this->formatOptionValue($option, $values[$option->id] ?? null)
            ];
        });
    }

    /**
     * Format option value for display
     */
    private function formatOptionValue($option, $value)
    {
        if ($value === null) {
            return null;
        }

        switch ($option->option_type) {
            case 'checkbox':
                return $value ? 'Yes' : 'No';
            case 'date':
                return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
            case 'file':
                return $value ? basename($value) : null;
            default:
                return $value;
        }
    }



 public function createTask(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employees = Employee::where('company_id', Auth::user()->company_id)->where('active', true)->get();

        return view('tasks.create', compact('job', 'employees'));
    }

    public function storeTask(Request $request, Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'task' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $task = Task::create([
            'job_id' => $job->id,
            'task' => $request->task,
            'description' => $request->description,
            'status' => $request->status,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id(),
        ]);

        foreach ($request->employee_ids as $employeeId) {
            $job->jobEmployees()->create([
                'employee_id' => $employeeId,
                'task_id' => $task->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_in_days' => $request->start_date && $request->end_date ?
                    \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 : null,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Task created and employees assigned successfully.');
    }

    public function editTask(Job $job, Task $task)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employees = Employee::where('company_id', Auth::user()->company_id)->where('active', true)->get();

        return view('tasks.edit', compact('job', 'task', 'employees'));
    }

    public function updateTask(Request $request, Job $job, Task $task)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'task' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        $task->update([
            'task' => $request->task,
            'description' => $request->description,
            'status' => $request->status,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id(),
        ]);

        // Delete existing job_employee records for this task
        $job->jobEmployees()->where('task_id', $task->id)->delete();

        // Create new job_employee records
        foreach ($request->employee_ids as $employeeId) {
            $job->jobEmployees()->create([
                'employee_id' => $employeeId,
                'task_id' => $task->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_in_days' => $request->start_date && $request->end_date ?
                    \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 : null,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Task updated successfully.');
    }

    public function destroyTask(Job $job, Task $task)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $task->update(['active' => false, 'updated_by' => Auth::id()]);
        $job->jobEmployees()->where('task_id', $task->id)->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('jobs.show', $job)->with('success', 'Task deleted successfully.');
    }

    public function createJobItem(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $items = Item::where('company_id', Auth::user()->company_id)
                     ->where('active', true)
                     ->get();
        return view('jobs.items.create', compact('job', 'items'));
    }

    public function storeJobItem(Request $request, Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $job->items()->attach($request->item_id, [
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('jobs.show', $job)->with('success', 'Item added to job successfully.');
    }
}
