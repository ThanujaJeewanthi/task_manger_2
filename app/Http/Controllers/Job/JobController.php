<?php

namespace App\Http\Controllers\Job;

use App\Models\Job;
use App\Models\Item;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\JobType;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\JobOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
   public function index(Request $request)
{
    $companyId = Auth::user()->company_id;

    // Initialize query
    $query = Job::with(['jobType', 'client', 'equipment'])
        ->where('company_id', $companyId)
        ->where('active', true);

    // Handle sorting
    $sortBy = $request->input('sort_by', 'priority'); // Default to priority
    $sortOrder = $request->input('sort_order', 'asc'); // Default to ascending

    // Validate sort_by to prevent SQL injection
    $allowedSorts = ['job_number', 'priority', 'start_date', 'due_date'];
    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'priority';
    }

    // Apply sorting
    if ($sortBy === 'priority') {
        $query->orderBy('priority', $sortOrder)
              ->orderBy('start_date', 'asc'); // Secondary sort by start_date
    } else {
        $query->orderBy($sortBy, $sortOrder);
    }

    // Handle filters
    if ($request->filled('job_type_id')) {
        $query->where('job_type_id', $request->input('job_type_id'));
    }

    if ($request->filled('client_id')) {
        $query->where('client_id', $request->input('client_id'));
    }

    if ($request->filled('equipment_id')) {
        $query->where('equipment_id', $request->input('equipment_id'));
    }

    if ($request->filled('job_number')) {
        $query->where('job_number', 'like', '%' . $request->input('job_number') . '%');
    }

    // Fetch filter options
    $jobTypes = JobType::all();
    $clients = Client::where('company_id', $companyId)->get();
    $equipments = Equipment::where('company_id', $companyId)->get();

    // Paginate results
    $jobs = $query->paginate(12);

    return view('jobs.index', compact('jobs', 'jobTypes', 'clients', 'equipments', 'sortBy', 'sortOrder'));
}

 public function create()
    {
        $companyId = Auth::user()->company_id;
        // return $companyId;
        $jobTypes = JobType::with(['jobOptions'])->where('active', true)->get();

        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        $employees = Employee::where('company_id', $companyId)->where('active', true)->get();

        // Get users with their roles for job assignment
        $users = User::with('userRole')
            ->where('company_id', $companyId)
            ->where('active', true)
            ->whereHas('userRole', function($query) {
                $query->where('active', true);
            })
            ->get();

        return view('jobs.create', compact('jobTypes', 'clients', 'equipments', 'employees', 'users'));
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

        $job->load(['jobType.jobOptions', 'client', 'equipment', 'jobEmployees.employee', 'jobEmployees.task']);
        $employees = Employee::where('company_id', Auth::user()->company_id)->where('active', true)->get();
        // tasks of that job
        $tasks = Task::where('job_id', $job->id)->where('active', true)->with('jobEmployees.employee')->get();

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
        return view('items.create', compact('job', 'items'));
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

     public function copy(Job $job)
    {

        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $jobTypes = JobType::with('jobOptions')->where('active', true)->get();
        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        // Decode job option values for editing
        $jobOptionValues = $job->job_option_values ? json_decode($job->job_option_values, true) : [];

        return view('jobs.copy', compact('job', 'jobTypes', 'clients', 'equipments', 'jobOptionValues'));
    }

    public function storeCopy(Request $request, Job $job)
    {


        // Get job type and its options for validation
        $jobType = null;
        if ($request->has('job_type_id') && $request->job_type_id) {
            $jobType = JobType::with('jobOptions')->find($request->job_type_id);
        }

        // Build validation rules
        $rules = $this->getValidationRules($jobType);
        $request->validate($rules);

        // Prepare job data
        $jobData = $this->prepareJobData($request);

        // Handle job option values
        $jobOptionValues = $this->processJobOptionValues($request, $jobType);
        if (!empty($jobOptionValues)) {
            $jobData['job_option_values'] = $jobOptionValues;
        }

        // Copy photos from original job
        if ($job->photos) {
            $jobData['photos'] = $job->photos; // Copy JSON-encoded photos
        }

        // Create the new job
        $newJob = Job::create($jobData);

        // Copy tasks from original job
        $tasks = Task::where('job_id', $job->id)->where('active', true)->get();
        foreach ($tasks as $task) {
            $newTask = $task->replicate();
            $newTask->job_id = $newJob->id;
            $newTask->created_by = Auth::id();
            $newTask->save();

            // Copy job employees for the task
            $jobEmployees = $job->jobEmployees()->where('task_id', $task->id)->get();
            foreach ($jobEmployees as $jobEmployee) {
                $newJob->jobEmployees()->create([
                    'employee_id' => $jobEmployee->employee_id,
                    'task_id' => $newTask->id,
                    'start_date' => $jobEmployee->start_date,
                    'end_date' => $jobEmployee->end_date,
                    'duration_in_days' => $jobEmployee->duration_in_days,
                    'status' => $jobEmployee->status,
                    'notes' => $jobEmployee->notes,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('jobs.index')->with('success', 'Job copied successfully as a new job.');
    }

    public function extendTask(Job $job)
    {


        $tasks = Task::where('job_id', $job->id)->where('active', true)->with('jobEmployees.employee')->get();
        return view('tasks.extend-task', compact('job', 'tasks'));
    }

    public function storeExtendTask(Request $request, Job $job)
    {


        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'new_end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Find the task and its job employees
        $task = Task::where('job_id', $job->id)->findOrFail($request->task_id);
        $jobEmployees = $job->jobEmployees()->where('task_id', $task->id)->get();

        // Calculate new duration
        $newEndDate = \Carbon\Carbon::parse($request->new_end_date);
        $startDate = $jobEmployees->first()->start_date ? \Carbon\Carbon::parse($jobEmployees->first()->start_date) : null;
        $durationInDays = $startDate ? $startDate->diffInDays($newEndDate) + 1 : null;

        // Get job type for validation
        $jobType = JobType::with('jobOptions')->find($job->job_type_id);

        // Create new job with updated due date
        $jobData = [
            'job_number' => $job->job_number . '-EXT-' . time(), // Unique job number
            'job_type_id' => $job->job_type_id,
            'client_id' => $job->client_id,
            'equipment_id' => $job->equipment_id,
            'description' => $job->description,
            'references' => $job->references,
            'priority' => $job->priority,
            'start_date' => $job->start_date,
            'due_date' => $newEndDate->format('Y-m-d'),
            'active' => true,
            'status' => $job->status,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id(),
            'photos' => $job->photos,
            'job_option_values' => $job->job_option_values,
        ];

        $newJob = Job::create($jobData);

        // Copy all tasks, updating the selected task's duration
        $tasks = Task::where('job_id', $job->id)->where('active', true)->get();
        foreach ($tasks as $existingTask) {
            $newTask = $existingTask->replicate();
            $newTask->job_id = $newJob->id;
            $newTask->created_by = Auth::id();

            if ($existingTask->id == $request->task_id) {
                $newTask->end_date = $newEndDate->format('Y-m-d');
            }

            $newTask->save();

            // Copy job employees, updating duration for the selected task
            $existingJobEmployees = $job->jobEmployees()->where('task_id', $existingTask->id)->get();
            foreach ($existingJobEmployees as $jobEmployee) {
                $newJob->jobEmployees()->create([
                    'employee_id' => $jobEmployee->employee_id,
                    'task_id' => $newTask->id,
                    'start_date' => $jobEmployee->start_date,
                    'end_date' => ($existingTask->id == $request->task_id) ? $newEndDate->format('Y-m-d') : $jobEmployee->end_date,
                    'duration_in_days' => ($existingTask->id == $request->task_id) ? $durationInDays : $jobEmployee->duration_in_days,
                    'status' => $jobEmployee->status,
                    'notes' => $jobEmployee->notes,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('jobs.index')->with('success', 'New job created with extended task duration.');
    }
}
