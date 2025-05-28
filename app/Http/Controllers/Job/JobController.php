<?php

namespace App\Http\Controllers\Job;

use App\Models\Job;
use App\Models\Item;
use App\Models\Task;
use App\Models\Client;
use App\Models\JobType;
use App\Models\Employee;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        // return $companyId;
        $jobTypes = JobType::with(['jobOptions'])->where('active', true)->get();

        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        $employees = Employee::whereHas('user', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->where('active', true)->get();

        return view('jobs.create', compact('jobTypes', 'clients', 'equipments', 'employees'));
    }

    // Add this new method to handle AJAX requests for job type options
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
    // Build validation rules dynamically
    $rules = [
        'job_number' => 'required|string|max:50|unique:jobs',
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

    // Add validation for job options if job_type_id is present
    if ($request->has('job_type_id') && $request->job_type_id) {
        $jobType = JobType::with('jobOptions')->find($request->job_type_id);
        if ($jobType) {
            foreach ($jobType->jobOptions as $option) {
                $fieldName = 'job_option_' . $option->id;

                if ($option->required) {
                    $rules[$fieldName] = 'required';
                }

                // Add specific validation based on option type
                switch ($option->option_type) {
                    case 'number':
                        $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'numeric';
                        break;
                    case 'date':
                        $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'date';
                        break;
                    case 'file':
                        $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'file|max:2048';
                        break;
                    case 'checkbox':
                        $rules[$fieldName] = 'nullable|boolean';
                        break;
                    case 'select':
                        // For equipment and client dropdowns
                        if ($option->id == 1) { // Equipment option
                            $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'exists:equipments,id';
                        } elseif ($option->id == 2) { // Client option
                            $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'exists:clients,id';
                        } else {
                            $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'string|max:255';
                        }
                        break;
                    default:
                        $rules[$fieldName] = ($option->required ? 'required|' : 'nullable|') . 'string|max:255';
                }
            }
        }
    }

    $request->validate($rules);

    $data = $request->all();
    $data['company_id'] = Auth::user()->company_id;
    $data['status'] = 'draft';
    $data['active'] = $request->has('is_active');
    $data['created_by'] = Auth::id();

    // Handle photo uploads
    if ($request->hasFile('photos')) {
        $photos = [];
        foreach ($request->file('photos') as $photo) {
            $photos[] = $photo->store('job_photos', 'public');
        }
        $data['photos'] = json_encode($photos);
    }

    // Handle job option values and map special ones to main fields
    $jobOptionValues = [];
    foreach ($request->all() as $key => $value) {
        if (strpos($key, 'job_option_') === 0) {
            $optionId = str_replace('job_option_', '', $key);

            // Map specific job options to main database fields
            if ($optionId == 1) { // Equipment option
                $data['equipment_id'] = $value;
            } elseif ($optionId == 2) { // Client option
                $data['client_id'] = $value;
            } else {
                // Handle file uploads for file type options
                if ($request->hasFile($key)) {
                    $jobOptionValues[$optionId] = $request->file($key)->store('job_option_files', 'public');
                } else {
                    $jobOptionValues[$optionId] = $value;
                }
            }
        }
    }

    if (!empty($jobOptionValues)) {
        $data['job_option_values'] = json_encode($jobOptionValues);
    }

    Job::create($data);

    return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
}

    public function show(Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $job->load(['jobType.jobOptions', 'client', 'equipment', 'jobEmployees.employee.user', 'jobEmployees.task']);
        $employees = Employee::whereHas('user', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->where('active', true)->get();
        $tasks = Task::where('active', true)->get();

        return view('jobs.show', compact('job', 'employees', 'tasks'));
    }

    public function edit(Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $jobTypes = JobType::with('jobOptions')->where('active', true)->get();
        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        return view('jobs.edit', compact('job', 'jobTypes', 'clients', 'equipments'));
    }

    public function update(Request $request, Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'job_number' => 'required|string|max:50|unique:jobs,job_number,' . $job->id,
            'job_type_id' => 'required|exists:job_types,id',
            'client_id' => 'nullable|exists:clients,id',
            'equipment_id' => 'nullable|exists:equipments,id',
            'description' => 'nullable|string',
            'references' => 'nullable|string',
            'status' => 'required|in:draft,pending,in_progress,on_hold,completed,cancelled',
            'priority' => 'required|in:1,2,3,4',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'completed_date' => 'nullable|date',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['active'] = $request->has('is_active');
        $data['updated_by'] = Auth::id();

        // Handle photo uploads
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
            $data['photos'] = json_encode($photos);
        }

        $job->update($data);

        return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $job->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    // app/Http/Controllers/Job/JobController.php
public function createTask(Job $job)
{
    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    $employees = Employee::where('company_id', Auth::user()->company_id)
                        ->where('active', true)
                        ->get();

    return view('jobs.tasks.create', compact('job', 'employees'));
}

public function storeTask(Request $request, Job $job)
{
    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    $request->validate([
        'task' => 'required|string|max:255',
        'description' => 'nullable|string',
        'cash_issued_date' => 'nullable|date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i|after:start_time',
        'status' => 'required|in:pending,in_progress,completed',
        'pending_reason' => 'nullable|string',
        'target_date' => 'nullable|date|after_or_equal:today',
        'employee_ids' => 'required|array',
        'employee_ids.*' => 'exists:employees,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'notes' => 'nullable|string',
    ]);

    $task = Task::create([
        'job_id' => $job->id,
        'task' => $request->task,
        'description' => $request->description,
        'cash_issued_date' => $request->cash_issued_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'status' => $request->status,
        'pending_reason' => $request->pending_reason,
        'target_date' => $request->target_date,
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
        ]);
    }

    return redirect()->route('jobs.show', $job)->with('success', 'Task created and employees assigned successfully.');
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
