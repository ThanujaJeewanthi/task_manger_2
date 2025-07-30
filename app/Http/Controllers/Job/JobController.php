<?php

namespace App\Http\Controllers\Job;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\Item;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\JobType;




use App\Models\Equipment;
use App\Models\JobOption;
use App\Models\JobUser;
use Illuminate\Http\Request;
use App\Models\JobActivityLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Services\JobActivityLogger;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\JobItems;

class JobController extends Controller
{
   public function index(Request $request)
{
    $companyId = Auth::user()->company_id;

    // Initialize query
    $query = Job::with(['jobType', 'client', 'equipment', 'jobUsers.user'])
        ->where('company_id', $companyId)
        ->where('active', true);

    // Get user role
    $userRole = Auth::user()->userRole->name ?? '';

    // Filter jobs based on user role
    switch($userRole) {
        case 'Super Admin':

            break;
        case 'Supervisor':
            $query->where('created_by', Auth::id());
            break;
        case 'User':

            $userId = Auth::user()->user->id ?? null;


                $query->whereIn('id', function($q) use ($userId) {
                    $q->select('job_id')
                      ->from('job_users')
                      ->where('user_id', $userId)
                      ->where('active', true); // Only get active assignments
                });

            break;

        case 'Engineer':
            // Show all active jobs within company (no additional filter needed)
            break;

        case 'Technical Officer':
            $query->where('assigned_user_id', Auth::id());
            break;

        default:
            // For any other role, show no jobs
            $query->where('id', 0);
            break;
    }

    // Handle sorting
    $sortBy = $request->input('sort_by', 'priority'); // Default to priority
    $sortOrder = $request->input('sort_order', 'asc'); // Default to ascending

    // Validate sort_by to prevent SQL injection
    $allowedSorts = ['id', 'priority', 'start_date', 'due_date'];
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

    if ($request->filled('id')) {
        $query->where('id', 'like', '%' . $request->input('id') . '%');
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

        $users = User::where('company_id', $companyId)->where('active', true)->get();

        // Get users where userRole is Technical officer and company is current company ,active =1
        $users = User::where('company_id', $companyId)
            ->whereHas('userRole', function ($query) {
                $query->where('name', 'Technical Officer');
            })
            ->where('active', true)
            ->get();

        return view('jobs.create', compact('jobTypes', 'clients', 'equipments', 'users', 'users'));
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

        'job_type_id' => 'required|exists:job_types,id',
        'client_id' => 'nullable|exists:clients,id',
        'equipment_id' => 'nullable|exists:equipments,id',
        'description' => 'nullable|string',
        'references' => 'nullable|string',
        'priority' => 'required|in:1,2,3,4',
        'assigned_user_id' => 'nullable|exists:users,id',
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
    $data['status'] = 'pending';
    $data['active'] = $request->has('is_active');
    $data['created_by'] = Auth::id();

    //assigned_user_id is assigned with the job ,he must be able to view job
    if ($request->has('assigned_user_id')) {
        $data['assigned_user_id'] = $request->input('assigned_user_id');
    }

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

    $job = Job::create($data);

// Log job creation with proper photo count
$photosCount = 0;
if ($request->hasFile('photos')) {
    $photosCount = count($request->file('photos'));
}

JobActivityLogger::logJobCreated($job, [
    'photos_count' => $photosCount,
    'job_options' => $jobOptionValues,
]);

// After job creation and initial logging
if (isset($data['assigned_user_id']) && $data['assigned_user_id']) {
    $assignedUser = User::find($data['assigned_user_id']);
    if ($assignedUser) {
        JobActivityLogger::logJobAssigned($job, $assignedUser, 'primary');
    }
}


    return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
}


         public function show(Job $job)
    {
        // Check if job belongs to current user's company
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Load relationships for timeline view
        $job->load([
            'jobType.jobOptions',
            'client',
            'equipment',
            'jobUsers.user',
            'jobUsers.task',
            'tasks.jobUsers.user',
            'tasks.taskExtensionRequests' => function($query) {
                $query->where('status', 'pending');
            }
        ]);

        // Get users and tasks (your existing code)
        $users = User::where('company_id', Auth::user()->company_id)->where('active', true)->get();
        $tasks = Task::where('job_id', $job->id)->where('active', true)->with('jobUsers.user')->get();
        $jobItems = JobItems::where('job_id', $job->id)->where('active', true)->get();

        // Get timeline data
        $timelineData = $this->getTimelineData($job);

        // Get basic job stats
        $jobStats = $this->getJobStats($job);



        // Get activity statistics
        $activityStats = JobActivityLogger::getJobActivityStats($job->id);

       $recentActivities = JobActivityLog::where('job_id', $job->id)
    ->where('job_activity_logs.active', true) // Specify table name to avoid ambiguity
    ->with(['user', 'affectedUser'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

        // Return your existing view with timeline data added
        return view('jobs.show', compact('job', 'users', 'tasks', 'jobItems', 'timelineData', 'jobStats','recentActivities', 'activityStats'));
        // Get recent activities (last 10)


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
    { if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        $oldValues = $job->getOriginal();

        $request->validate([

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
        ]);

        $data = $request->all();
        $data['active'] = $request->has('is_active');
        $data['updated_by'] = Auth::id();        $data['updated_by'] = Auth::id();

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




        // Log job update with old and new values
        JobActivityLogger::logJobUpdated($job, $oldValues, $data);

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


        $users = User::where('company_id', Auth::user()->company_id)
            ->where('active', true)
            ->whereHas('userRole', function ($query) {
            $query->whereNotIn('name', ['Admin', 'Super Admin']);
            })
            ->get();
        return view('tasks.create', compact('job',  'users'));
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
        'start_time'=> 'nullable|date_format:H:i',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'end_time'=> 'nullable|date_format:H:i',
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id',
        'notes' => 'nullable|string',
    ]);

    $task = Task::create([
        'task' => $request->task,
        'description' => $request->description,
        'job_id'=> $job->id,
        'status' => 'pending',
        'active' => $request->has('is_active'),
        'created_by' => Auth::id(),
    ]);

    foreach ($request->user_ids as $userId) {
        $job->jobUsers()->create([
            'user_id' => $userId,
            'task_id' => $task->id,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            // UPDATED: Calculate total duration as real days with decimal precision
            'duration' => ($request->start_date && $request->start_time && $request->end_date && $request->end_time)
                ? (Carbon::parse($request->start_date . ' ' . $request->start_time)
                    ->floatDiffInRealDays(Carbon::parse($request->end_date . ' ' . $request->end_time)))
                : null,
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    // log task creation
    $assignedUsers = User::whereIn('id', $request->user_ids)->get();
    JobActivityLogger::logTaskCreated($job, $task, $assignedUsers);

    return redirect()->route('jobs.show', $job)->with('success', 'Task created and users assigned successfully.');
}

    public function editTask(Job $job, Task $task)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $users = User::where('company_id', Auth::user()->company_id)
            ->where('active', true)
            ->whereHas('userRole', function ($query) {
            $query->whereNotIn('name', ['Admin', 'Super Admin']);
            })
            ->get();
        return view('tasks.edit', compact('job', 'task', 'users'));
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
        'start_time'=> 'nullable|date_format:H:i',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'end_time'=> 'nullable|date_format:H:i',
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id',
        'notes' => 'nullable|string',
    ]);

    $task->update([
        'task' => $request->task,
        'job_id'=>$job->id,
        'description' => $request->description,
        'status' => 'pending',
        'active' => $request->has('is_active'),
        'updated_by' => Auth::id(),
    ]);

    // Delete existing job_user records for this task
    $job->jobUsers()->where('task_id', $task->id)->delete();

    // Create new job_user records
    foreach ($request->user_ids as $userId) {
        $job->jobUsers()->create([
            'user_id' => $userId,
            'task_id' => $task->id,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            // UPDATED: Calculate total duration as real days with decimal precision
            'duration' => ($request->start_date && $request->start_time && $request->end_date && $request->end_time)
                ? (Carbon::parse($request->start_date . ' ' . $request->start_time)
                    ->floatDiffInRealDays(Carbon::parse($request->end_date . ' ' . $request->end_time)))
                : null,
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    // log task update
    $assignedUsers = User::whereIn('id', $request->user_ids)->get();
    JobActivityLogger::logTaskUpdated($job, $task, $assignedUsers);

    return redirect()->route('jobs.show', $job)->with('success', 'Task updated successfully.');
}

    public function destroyTask(Job $job, Task $task)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $task->update(['active' => false, 'updated_by' => Auth::id()]);
        $job->jobUsers()->where('task_id', $task->id)->update(['active' => false, 'updated_by' => Auth::id()]);
// log task deletion
        JobActivityLogger::logTaskDeleted($job, $task);
        return redirect()->route('jobs.show', $job)->with('success', 'Task deleted successfully.');
    }

 public function addItems(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $items = Item::where('company_id', Auth::user()->company_id)
                     ->where('active', true)
                     ->get();

        // Get all current job items with job id
        $jobItems = JobItems::where('job_id', $job->id)
            ->where('active', true)
            ->with('item')  // Load the related item model
            ->get(['id', 'job_id', 'item_id', 'quantity', 'notes', 'issue_description',
                   'custom_item_description', 'added_by', 'added_at', 'addition_stage', 'active']);

        // Get users with approval role whose role is admin,engineer
        $approvalUsers = User::where('company_id', Auth::user()->company_id)
            ->whereHas('userRole', function ($query) {
                $query->whereIn('name', ['Engineer', 'admin']);
            })
            ->where('active', true)
            ->get();
        return view('jobs.items.add', compact('job', 'items', 'jobItems', 'approvalUsers'));
    }

    /**
     * Store items added to a job
     */
public function storeItems(Request $request, Job $job)
   {

    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    $request->validate([
        'issue_description' => 'required|string|max:1000',
        'items' => 'nullable|array',
        'items.*.item_id' => 'nullable|exists:items,id',
        'items.*.quantity' => 'nullable|numeric|min:0.01',
        'items.*.notes' => 'nullable|string|max:500',
        'new_items' => 'nullable|array',
        'new_items.*.description' => 'nullable|string|max:500',
        'new_items.*.quantity' => 'nullable|numeric|min:0.01',
        'close_job' => 'nullable|boolean',
        'request_approval_from' => 'nullable|exists:users,id',
    ]);

    // If closing job as minor issue
    if ($request->has('close_job') && $request->close_job) {
        $job->update([
            'status' => 'completed',
            'approval_status' => 'approved',
             'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => 'Completed as minor issue: ' . $request->issue_description,
            'completed_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('jobs.show', $job)->with('success', 'Job closed successfully as minor issue.');
    }

    // Check if job is already in approval process
    $isInApprovalProcess = $job->approval_status === 'requested';

    // Get current job items to check for existing ones
    $currentJobItems = DB::table('job_items')
        ->where('job_id', $job->id)
        ->where('active', true)
        ->get()
        ->keyBy('item_id');

    // Store existing items
    if ($request->has('items')) {
        foreach ($request->items as $itemData) {
            if (!empty($itemData['item_id']) && !empty($itemData['quantity'])) {

                $itemId = $itemData['item_id'];

                // Check if this item already exists for this job
                if ($currentJobItems->has($itemId)) {
                    // Update existing item: sum the quantities
                    $existingItem = $currentJobItems->get($itemId);
                    $newQuantity = $existingItem->quantity + $itemData['quantity'];

                    DB::table('job_items')
                        ->where('id', $existingItem->id)
                        ->update([
                            'quantity' => $newQuantity,
                            'notes' => $itemData['notes'] ?? $existingItem->notes,
                            'issue_description' => $request->issue_description,
                            'added_by' => Auth::id(),
                            'added_at' => now(),
                            'updated_by' => Auth::id(),
                            'updated_at' => now(),
                        ]);
                } else {

                    DB::table('job_items')->insert([
                        'job_id' => $job->id,
                        'item_id' => $itemId,
                        'quantity' => $itemData['quantity'],
                        'notes' => $itemData['notes'] ?? null,
                        'issue_description' => $request->issue_description,
                        'added_by' => Auth::id(),
                        'added_at' => now(),
                        'addition_stage' => 'job_approval',
                        'active' => true,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    // Store new item descriptions (items not in inventory)
    if ($request->has('new_items')) {
        foreach ($request->new_items as $newItem) {
            if (!empty($newItem['description']) && !empty($newItem['quantity'])) {



                DB::table('job_items')->insert([
                    'job_id' => $job->id,
                    'item_id' => null, // null for custom items
                    'custom_item_description' => $newItem['description'],
                    'quantity' => $newItem['quantity'],
                    'issue_description' => $request->issue_description,
                    'added_by' => Auth::id(),
                    'added_at' => now(),
                    'addition_stage' => 'job_approval',
                    'active' => true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // Handle approval request logic
    if ($request->has('request_approval_from') && $request->request_approval_from) {
        // Only change approval status if not already in approval process
        if (!$isInApprovalProcess) {
            $job->update([
                'approval_status' => 'requested',
                'request_approval_from' => $request->request_approval_from,
                'updated_by' => Auth::id(),
            ]);

            $message = 'Items added and approval requested successfully.';
        } else {
            // Job already in approval, just update the timestamp
            $job->update([
                'updated_by' => Auth::id(),
            ]);

            $message = 'Additional items added to existing approval request.';
        }
    } else {
        // No approval requested, just update the job
        $job->update([
            'updated_by' => Auth::id(),
        ]);

        if ($isInApprovalProcess) {
            $message = 'Additional items added. Job remains in approval process.';
        } else {
            $message = 'Items added to job successfully.';
        }
    }
    // job activity log
  JobActivityLogger::logJobItemsAdded($job, $request->items ?? [], $request->new_items ?? []);


    return redirect()->route('jobs.show', $job)->with('success', $message);
}

    /**
     * Show job approval interface
     */
    public function showApproval(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Check if user has approval rights
        $userRole = Auth::user()->userRole->name ?? '';
        if (!in_array($userRole, ['Engineer', 'admin'])) {
            abort(403, 'You do not have permission to approve jobs.');
        }

        $job->load(['jobType', 'client', 'equipment']);

        // Get job items with their details
      $jobItems = JobItems::where('job_id', $job->id)
            ->where('active', true)
            ->get();



        // Get all items for potential editing
        $items = Item::where('company_id', Auth::user()->company_id)
                     ->where('active', true)
                     ->get();

        return view('jobs.approve', compact('job', 'jobItems', 'items'));
    }

    /**
     * Process job approval
     */

public function processApproval(Request $request, Job $job)
{
    // Check company access and permissions
    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    $request->validate([
        'action' => 'required|in:approve,reject',
        'approval_notes' => 'nullable|string|max:1000',
        // Add validation for item edits
        'items' => 'nullable|array',
        'items.*.quantity' => 'nullable|numeric|min:0.01',
        'items.*.notes' => 'nullable|string|max:500',
        'additional_items' => 'nullable|array',
        'additional_items.*.item_id' => 'nullable|exists:items,id',
        'additional_items.*.quantity' => 'nullable|numeric|min:0.01',
        'additional_items.*.notes' => 'nullable|string|max:500',
        'new_items' => 'nullable|array',
        'new_items.*.description' => 'nullable|string|max:500',
        'new_items.*.quantity' => 'nullable|numeric|min:0.01',
    ]);

    try {
        DB::beginTransaction();

        $action = $request->action;

        // Process item edits if provided (regardless of approve/reject)
        if ($request->has('items')) {
            foreach ($request->items as $jobItemId => $itemData) {
                if (!empty($itemData['quantity'])) {
                    DB::table('job_items')
                        ->where('id', $jobItemId)
                        ->where('job_id', $job->id)
                        ->where('active', true)
                        ->update([
                            'quantity' => $itemData['quantity'],
                            'notes' => $itemData['notes'] ?? null,
                            'updated_by' => Auth::id(),
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // Add additional items from inventory
        if ($request->has('additional_items')) {
            foreach ($request->additional_items as $itemData) {
                if (!empty($itemData['item_id']) && !empty($itemData['quantity'])) {
                    // Check if this item already exists for this job
                    $existingJobItem = DB::table('job_items')
                        ->where('job_id', $job->id)
                        ->where('item_id', $itemData['item_id'])
                        ->where('active', true)
                        ->first();

                    if ($existingJobItem) {
                        // Update existing item: sum the quantities
                        $newQuantity = $existingJobItem->quantity + $itemData['quantity'];
                        DB::table('job_items')
                            ->where('id', $existingJobItem->id)
                            ->update([
                                'quantity' => $newQuantity,
                                'notes' => $itemData['notes'] ?? $existingJobItem->notes,
                                'updated_by' => Auth::id(),
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Create new job item
                        DB::table('job_items')->insert([
                            'job_id' => $job->id,
                            'item_id' => $itemData['item_id'],
                            'quantity' => $itemData['quantity'],
                            'notes' => $itemData['notes'] ?? null,
                            'issue_description' => 'Added during approval process',
                            'added_by' => Auth::id(),
                            'added_at' => now(),
                            'addition_stage' => 'job_approval',
                            'active' => true,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Add new items (not in inventory)
        if ($request->has('new_items')) {
            foreach ($request->new_items as $newItem) {
                if (!empty($newItem['description']) && !empty($newItem['quantity'])) {
                    DB::table('job_items')->insert([
                        'job_id' => $job->id,
                        'item_id' => null, // null for custom items
                        'custom_item_description' => $newItem['description'],
                        'quantity' => $newItem['quantity'],
                        'issue_description' => 'Added during approval process',
                        'added_by' => Auth::id(),
                        'added_at' => now(),
                        'addition_stage' => 'job_approval',
                        'active' => true,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($action === 'approve') {
            $job->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
                'updated_by' => Auth::id(),
            ]);

            // Log approval
            JobActivityLogger::logJobApproval($job, 'approved', $request->approval_notes);

            $message = 'Job approved successfully! You can now add tasks.';
        } else {
            $job->update([
                'approval_status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_notes' => $request->approval_notes,
                'updated_by' => Auth::id(),
            ]);

            // Log rejection
            JobActivityLogger::logJobApproval($job, 'rejected', $request->approval_notes);

            $message = 'Job rejected successfully.';
        }

        DB::commit();
// redirect to the task creation page for this job
        return redirect()->route('jobs.tasks.create' , ['job' => $job->id])
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Failed to process approval. Please try again.');
    }
}
     private function getApprovalUserId()
    {
        // This should implement your business logic for determining who should approve
        // For example, find an engineer in the same company
        $companyId = Auth::user()->company_id;

        $engineer =User::whereHas('userRole', function ($query) {
                $query->where('name', 'Engineer');
            })
            ->where('company_id', $companyId)
            ->first();

        return $engineer ? $engineer->id : null;
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

        // Build validation rules dynamically
        $rules = [
            'job_type_id' => 'required|exists:job_types,id',
            'client_id' => 'nullable|exists:clients,id',
            'equipment_id' => 'nullable|exists:equipments,id',
            'description' => 'nullable|string',
            'references' => 'nullable|string',
            'priority' => 'required|in:1,2,3,4',
            'assigned_user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Add validation for job options if job_type_id is present
        if ($jobType) {
            foreach ($jobType->jobOptions as $option) {
                $fieldName = 'job_option_' . $option->id;

                if ($option->required) {
                    $rules[$fieldName] = 'required';
                // Copy job option values from original job (if no new values provided)
            if (empty($jobOptionValues)) {
                $originalJobOptionValues = \App\Models\JobOptionValue::where('job_id', $job->id)->get();
                foreach ($originalJobOptionValues as $originalValue) {
                    \App\Models\JobOptionValue::create([
                        'job_id' => $newJob->id,
                        'job_option_id' => $originalValue->job_option_id,
                        'value' => $originalValue->value,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
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
                        break;
                }
            }
        }

        // Validate the request
        $request->validate($rules);

        // Prepare job data (without custom id - use auto-increment)
        $jobData = [
            'job_type_id' => $request->job_type_id,
            'client_id' => $request->client_id,
            'equipment_id' => $request->equipment_id,
            'description' => $request->description,
            'references' => $request->references,
            'priority' => $request->priority,
            'assigned_user_id' => $request->assigned_user_id,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'active' => true,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id(),
        ];

        // Handle job option values in separate table (skip special options that map to main fields)
        if ($jobType) {
            foreach ($jobType->jobOptions as $option) {
                $fieldName = 'job_option_' . $option->id;

                // Skip special options that map to main database fields
                if ($option->id == 1) { // Equipment option
                    if ($request->has($fieldName)) {
                        $jobData['equipment_id'] = $request->input($fieldName);
                    }
                    continue;
                } elseif ($option->id == 2) { // Client option
                    if ($request->has($fieldName)) {
                        $jobData['client_id'] = $request->input($fieldName);
                    }
                    continue;
                }

                if ($request->has($fieldName)) {
                    $value = $request->input($fieldName);

                    // Handle file uploads for job options
                    if ($option->option_type === 'file' && $request->hasFile($fieldName)) {
                        $file = $request->file($fieldName);
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('job_option_files', $fileName, 'public');
                        $value = $filePath;
                    }

                    $jobOptionValues[$option->id] = $value;
                }
            }
        }

        if (!empty($jobOptionValues)) {
            $jobData['job_option_values'] = json_encode($jobOptionValues);
        }

        // Handle photo uploads for the new job (if any new photos are uploaded)
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $fileName = time() . '_' . $photo->getClientOriginalName();
                $filePath = $photo->storeAs('job_photos', $fileName, 'public');
                $photos[] = $filePath;
            }
            $jobData['photos'] = json_encode($photos);
        } else {
            // Copy photos from original job if no new photos uploaded
            if ($job->photos) {
                $jobData['photos'] = $job->photos; // Copy JSON-encoded photos
            }
        }

        try {
            DB::beginTransaction();

            // Create the new job
            $newJob = Job::create($jobData);

            // Save job option values to separate table
            if (!empty($jobOptionValues)) {
                foreach ($jobOptionValues as $optionId => $value) {
                    if ($value !== null && $value !== '') {
                        \App\Models\JobOptionValue::create([
                            'job_id' => $newJob->id,
                            'job_option_id' => $optionId,
                            'value' => $value, // Store file path in value column
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ]);
                    }
                }
            }

            // Copy tasks from original job
            $tasks = Task::where('job_id', $job->id)->where('active', true)->get();
            foreach ($tasks as $task) {
                $newTask = $task->replicate();
                $newTask->job_id = $newJob->id;
                $newTask->created_by = Auth::id();
                $newTask->save();

                // Copy job users for the task
                $jobUsers = $job->jobUsers()->where('task_id', $task->id)->get();
                foreach ($jobUsers as $jobUser) {
                    $newJob->jobUsers()->create([
                        'user_id' => $jobUser->user_id,
                        'task_id' => $newTask->id,
                        'start_date' => $jobUser->start_date,
                        'end_date' => $jobUser->end_date,
                        'duration_in_days' => $jobUser->duration_in_days,
                        'status' => 'pending', // Reset status for copied job
                        'notes' => $jobUser->notes,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            // Copy job items from original job (if any)
            $jobItems = JobItems::where('job_id', $job->id)->where('active', true)->get();
            foreach ($jobItems as $jobItem) {
                $newJobItem = $jobItem->replicate();
                $newJobItem->job_id = $newJob->id;
                $newJobItem->created_by = Auth::id();
                $newJobItem->updated_by = Auth::id();
                $newJobItem->added_by = Auth::id();
                $newJobItem->added_at = now();
                $newJobItem->save();
            }

            DB::commit();

            // Log job copy activity
            if (class_exists('App\Services\JobActivityLogger')) {
                \App\Services\JobActivityLogger::logJobCopied($newJob, $job, Auth::id());
            }

            return redirect()->route('jobs.index')->with('success', 'Job copied successfully as a new job.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to copy job. Please try again. Error: ' . $e->getMessage());
        }
    }
}
    public function extendTask(Job $job)
    {
      $tasks = Task::where('job_id', $job->id)->where('active', true)->with('jobUsers.user')->get();
        return view('tasks.extend-task', compact('job', 'tasks'));
    }
public function storeExtendTask(Request $request, Job $job)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'new_end_date' => 'required|date|after_or_equal:start_date',
        'new_end_time' => 'nullable|date_format:H:i', // ADDED time validation
    ]);

    // Find the task and its job users
    $task = Task::where('job_id', $job->id)->findOrFail($request->task_id);
    $jobUsers = $job->jobUsers()->where('task_id', $task->id)->get();

    // UPDATED: Calculate new duration with time components
    $newEndDate = Carbon::parse($request->new_end_date);
    $newEndTime = $request->new_end_time ? Carbon::parse($request->new_end_time) : Carbon::parse('23:59:59');
    $newEndDateTime = Carbon::parse($newEndDate->format('Y-m-d') . ' ' . $newEndTime->format('H:i:s'));

    // Create new job with updated due date
    $jobData = [
        'job_type_id' => $job->job_type_id,
        'client_id' => $job->client_id,
        'equipment_id' => $job->equipment_id,
        'description' => $job->description,
        'references' => $job->references,
        'priority' => $job->priority,
        'start_date' => $job->start_date,
        'due_date' => $newEndDateTime->format('Y-m-d'),
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
        $newTask->save();

        // Copy job users, updating duration for the selected task
        $existingJobUsers = $job->jobUsers()->where('task_id', $existingTask->id)->get();
        foreach ($existingJobUsers as $jobUser) {
            $endDate = ($existingTask->id == $request->task_id) ? $newEndDateTime->format('Y-m-d') : $jobUser->end_date;
            $endTime = ($existingTask->id == $request->task_id) ? $newEndDateTime->format('H:i:s') : $jobUser->end_time;

            // UPDATED: Calculate new duration with time precision
            $duration = null;
            if ($jobUser->start_date && $jobUser->start_time && $endDate && $endTime) {
                $startDateTime = Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                $endDateTime = Carbon::parse($endDate . ' ' . $endTime);
                $duration = $startDateTime->floatDiffInRealDays($endDateTime);
            }

            $newJob->jobUsers()->create([
                'user_id' => $jobUser->user_id,
                'task_id' => $newTask->id,
                'start_date' => $jobUser->start_date,
                'start_time' => $jobUser->start_time,
                'end_date' => $endDate,
                'end_time' => $endTime,
                'duration' => $duration,
                'status' => $jobUser->status,
                'notes' => $jobUser->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    // Log task extension
    $user = User::where('user_id', Auth::id())->first();
    JobActivityLogger::logTaskExtended($job, $task, $jobUsers->first()->end_date, $request->new_end_date, $user);

    return redirect()->route('jobs.index')->with('success', 'New job created with extended task duration.');
}


// Add these methods to app/Http/Controllers/Job/JobController.php

/**
 * Auto-update job status based on task status changes
 */
public function updateJobStatusBasedOnTasks(Job $job)
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
            'completed_date' => now()->format('Y-m-d H:i:s'),
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        // Log job completion
        \App\Services\JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'All tasks completed');
        \App\Services\JobActivityLogger::logJobCompleted($job, 'All tasks have been completed successfully');
    }
    // If at least one task is in progress and job is approved, mark job as in_progress
    elseif ($inProgressTasks > 0 && $currentStatus === 'approved') {
        $newStatus = 'in_progress';
        $job->update([
            'status' => $newStatus,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        // Log status change
        \App\Services\JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'Tasks started');
    }
    // If job has completed tasks but not all, and current status is in_progress, keep it in_progress
    elseif ($completedTasks > 0 && $completedTasks < $totalTasks && $currentStatus !== 'in_progress' && $currentStatus === 'approved') {
        $newStatus = 'in_progress';
        $job->update([
            'status' => $newStatus,
            'updated_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        // Log status change
        \App\Services\JobActivityLogger::logJobStatusChanged($job, $currentStatus, $newStatus, 'Partial task completion');
    }
}

/**
 * Show job review interface for engineers
 */
public function showReview(Job $job)
{
    // Check company access
    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    // Check user role permissions
    $userRole = Auth::user()->userRole->name ?? '';
    if (!in_array($userRole, ['Engineer', 'admin'])) {
        abort(403, 'You do not have permission to review jobs.');
    }

    // CRITICAL: Validate job status - only completed jobs can be reviewed
    if ($job->status !== 'completed') {
        return redirect()->route('jobs.show', $job)
            ->with('error', 'Only completed jobs can be reviewed. Current status: ' . ucfirst($job->status));
    }

    // Check if job is already closed/reviewed
    if ($job->status === 'closed') {
        return redirect()->route('jobs.show', $job)
            ->with('info', 'This job has already been reviewed and closed.');
    }

    // Validate that all tasks are actually completed
    $incompleteTasks = $job->tasks()->where('active', true)
        ->where('status', '!=', 'completed')
        ->count();

    if ($incompleteTasks > 0) {
        return redirect()->route('jobs.show', $job)
            ->with('error', "Cannot review job: {$incompleteTasks} task(s) are not completed yet.");
    }

    $job->load(['tasks.jobUsers.user', 'jobType', 'client', 'creator']);

    return view('jobs.review', compact('job'));
}

/**
 * Process job review and closure
 */
public function processReview(Request $request, Job $job)
{
    // Check company access
    if ($job->company_id !== Auth::user()->company_id) {
        abort(403);
    }

    // Check user role permissions
    $userRole = Auth::user()->userRole->name ?? '';
    if (!in_array($userRole, ['Engineer', 'admin'])) {
        abort(403, 'You do not have permission to review jobs.');
    }

    // Validate job status
    if ($job->status !== 'completed') {
        return redirect()->back()
            ->with('error', 'Only completed jobs can be reviewed and closed.');
    }

    // Check if already closed
    if ($job->status === 'closed') {
        return redirect()->route('jobs.show', $job)
            ->with('error', 'This job has already been closed.');
    }

    $request->validate([
        'action' => 'required|in:close',
        'review_notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        // Double-check all tasks are completed before closing
        $incompleteTasks = $job->tasks()->where('active', true)
            ->where('status', '!=', 'completed')
            ->exists();

        if ($incompleteTasks) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Cannot close job: Some tasks are not completed yet.');
        }

        // Close the job
        $job->update([
            'status' => 'closed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
            'closed_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Log the review - wrap in try-catch to prevent transaction failure
        try {
            if (class_exists('App\Services\JobActivityLogger')) {
                JobActivityLogger::logJobReviewed($job, $request->review_notes, Auth::id());
            }
        } catch (\Exception $logError) {
            // Log the error but don't fail the transaction
            \Log::warning('Failed to log job review activity', [
                'job_id' => $job->id,
                'error' => $logError->getMessage()
            ]);
        }

        DB::commit();

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job has been reviewed and closed successfully.');

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Failed to process job review', [
            'job_id' => $job->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        return redirect()->back()
            ->with('error', 'Failed to process review. Please try again. Error: ' . $e->getMessage());
    }
}

// Add method to get status color for consistent display
public static function getStatusColor($status)
{
    $statusColors = [
        'pending' => 'warning',
        'approved' => 'info',
        'in_progress' => 'primary',
        'on_hold' => 'secondary',
        'completed' => 'success',
        'closed' => 'dark',  // NEW: Different color for closed
        'cancelled' => 'danger'
    ];

    return $statusColors[$status] ?? 'secondary';
}

// Add method to get status label for consistent display
public static function getStatusLabel($status)
{
    $statusLabels = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'closed' => 'Closed ✓',  // NEW: Special label for closed
        'cancelled' => 'Cancelled'
    ];

    return $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

// timeline
private function getTimelineData(Job $job)
{
    $tasks = $job->tasks()
        ->where('active', true)
        ->with([
            'jobUsers' => function($query) {
                $query->with('user');
            },
            'taskExtensionRequests' => function($query) {
                $query->where('status', 'pending');
            }
        ])
        ->get();

    $timelineData = [
        'job_start' => $job->start_date ? Carbon::parse($job->start_date) : null,
        'job_end' => $job->due_date ? Carbon::parse($job->due_date) : null,
        'tasks' => collect()
    ];

    foreach ($tasks as $task) {
        $taskUsers = $task->jobUsers;

        if ($taskUsers->isEmpty()) {
            continue;
        }

        // UPDATED: Find earliest and latest times with precision
        $earliestStart = null;
        $latestEnd = null;

        foreach ($taskUsers as $jobUser) {
            if ($jobUser->start_date && $jobUser->start_time) {
                $startDateTime = Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                if (!$earliestStart || $startDateTime->lt($earliestStart)) {
                    $earliestStart = $startDateTime;
                }
            }

            if ($jobUser->end_date && $jobUser->end_time) {
                $endDateTime = Carbon::parse($jobUser->end_date->format('Y-m-d') . ' ' . $jobUser->end_time->format('H:i:s'));
                if (!$latestEnd || $endDateTime->gt($latestEnd)) {
                    $latestEnd = $endDateTime;
                }
            }
        }

        $progress = $this->calculateTaskProgress($task, $taskUsers);
        $hasExtensionRequest = $task->taskExtensionRequests->isNotEmpty();

        // UPDATED: Calculate total planned duration
        $plannedDuration = null;
        if ($earliestStart && $latestEnd) {
            $plannedDuration = $earliestStart->floatDiffInRealDays($latestEnd);
        }

        $timelineData['tasks']->push([
            'id' => $task->id,
            'name' => $task->task,
            'description' => $task->description,
            'status' => $task->status,
            'start_date_time' => $earliestStart,
            'end_date_time' => $latestEnd,
            'planned_duration' => $plannedDuration,
            'formatted_duration' => $plannedDuration ? self::formatDuration($plannedDuration) : 'Not set',
            'progress' => $progress,
            'users' => $taskUsers->map(function($jobUser) {
                return [
                    'id' => $jobUser->user->id,
                    'name' => $jobUser->user->name,
                    'initials' => $this->getInitials($jobUser->user->name),
                    'individual_duration' => $jobUser->duration ? self::formatDuration($jobUser->duration) : 'Not set',
                    'status' => $jobUser->status
                ];
            }),
            'has_extension_request' => $hasExtensionRequest
        ]);
    }

    return $timelineData;
}

public static function formatDuration($durationInRealDays)
{
    if (!$durationInRealDays || $durationInRealDays <= 0) {
        return '0 minutes';
    }

    $days = floor($durationInRealDays);
    $hours = ($durationInRealDays - $days) * 24;
    $wholeHours = floor($hours);
    $minutes = ($hours - $wholeHours) * 60;

    $formatted = '';
    if ($days > 0) {
        $formatted .= $days . ' day' . ($days !== 1 ? 's' : '');
    }
    if ($wholeHours > 0) {
        $formatted .= ($formatted ? ', ' : '') . $wholeHours . ' hour' . ($wholeHours !== 1 ? 's' : '');
    }
    if ($minutes > 0) {
        $formatted .= ($formatted ? ', ' : '') . round($minutes) . ' minute' . (round($minutes) !== 1 ? 's' : '');
    }

    return $formatted ?: '0 minutes';
}
private function calculateTaskProgress(Task $task, $taskUsers)
{
    if ($task->status === 'completed') return 100;
    if ($task->status === 'cancelled') return 0;
    if ($task->status === 'pending') return 0;

    // For in-progress tasks, calculate based on user completion percentage
    $totalUsers = $taskUsers->count();

    if ($totalUsers === 0) return 0;

    $completedUsers = $taskUsers->where('status', 'completed')->count();
    $inProgressUsers = $taskUsers->where('status', 'in_progress')->count();

    // Calculate completion percentage
    $completionPercentage = ($completedUsers / $totalUsers) * 100;

    // If some users are in progress but none completed, show partial progress
    if ($completionPercentage === 0 && $inProgressUsers > 0) {
        // Calculate time-based progress for in-progress users
        $startDateTime = null;
        $endDateTime = null;

        // Find earliest start and latest end across all users
        foreach ($taskUsers as $jobUser) {
            if ($jobUser->start_date && $jobUser->start_time) {
                $userStart = Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                if (!$startDateTime || $userStart->lt($startDateTime)) {
                    $startDateTime = $userStart;
                }
            }

            if ($jobUser->end_date && $jobUser->end_time) {
                $userEnd = Carbon::parse($jobUser->end_date->format('Y-m-d') . ' ' . $jobUser->end_time->format('H:i:s'));
                if (!$endDateTime || $userEnd->gt($endDateTime)) {
                    $endDateTime = $userEnd;
                }
            }
        }

        if ($startDateTime && $endDateTime) {
            $now = Carbon::now();

            if ($now <= $startDateTime) return 5; // Just started
            if ($now >= $endDateTime) return 85; // Overdue but not completed

            $totalHours = $startDateTime->diffInRealHours($endDateTime);
            if ($totalHours <= 0) return 50; // Same time task

            $elapsedHours = $startDateTime->diffInRealHours($now);
            $timeProgress = min(80, ($elapsedHours / $totalHours) * 80); // Max 80% for time-based

            return round($timeProgress);
        }

        return 25; // Default for in-progress with no times
    }

    return round($completionPercentage);
}
   private function getJobStats(Job $job)
{
    $tasks = $job->tasks()->where('active', true)->get();
    $totalTasks = $tasks->count();

    if ($totalTasks === 0) {
        return [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'in_progress_tasks' => 0,
            'pending_tasks' => 0,
            'cancelled_tasks' => 0,
            'overall_progress' => 0,
            'user_assignments' => 0,
            'completed_assignments' => 0
        ];
    }

    $completedTasks = $tasks->where('status', 'completed')->count();
    $inProgressTasks = $tasks->where('status', 'in_progress')->count();
    $pendingTasks = $tasks->where('status', 'pending')->count();
    $cancelledTasks = $tasks->where('status', 'cancelled')->count();

    // Calculate overall progress based on completed tasks vs total tasks
    $overallProgress = round(($completedTasks / $totalTasks) * 100);

    // Get user assignment statistics
    $totalAssignments = \App\Models\JobUser::whereIn('task_id', $tasks->pluck('id'))->count();
    $completedAssignments = \App\Models\JobUser::whereIn('task_id', $tasks->pluck('id'))
        ->where('status', 'completed')->count();

    return [
        'total_tasks' => $totalTasks,
        'completed_tasks' => $completedTasks,
        'in_progress_tasks' => $inProgressTasks,
        'pending_tasks' => $pendingTasks,
        'cancelled_tasks' => $cancelledTasks,
        'overall_progress' => $overallProgress,
        'user_assignments' => $totalAssignments,
        'completed_assignments' => $completedAssignments,
        'assignment_completion_rate' => $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0
    ];
}
    private function getInitials($name)
    {
        $words = explode(' ', $name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    // API endpoint for timeline data (for AJAX updates)
    public function getTimelineJson(Job $job)
    {
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return response()->json([
            'timeline' => $this->getTimelineData($job),
            'stats' => $this->getJobStats($job)
        ]);
    }

 // UPDATED: getTaskDetails method in JobController
public function getTaskDetails(Job $job, Task $task)
{
    Log::info('getTaskDetails called', ['job_id' => $job->id, 'task_id' => $task->id]);

    if ($job->company_id !== Auth::user()->company_id || $task->job_id !== $job->id) {
        abort(403);
    }

    $task->load([
        'jobUsers' => function($query) {
            $query->with('user');
        },
        'taskExtensionRequests' => function($query) {
            $query->where('status', 'pending');
        }
    ]);

    $taskUsers = $task->jobUsers;
    $progress = $this->calculateTaskProgress($task, $taskUsers);

    return response()->json([
        'task' => [
            'id' => $task->id,
            'name' => $task->task,
            'description' => $task->description ?: 'No description provided',
            'status' => $task->status,
            'progress' => $progress
        ],
        'users' => $taskUsers->map(function($jobUser) {
            return [
                'name' => $jobUser->user->name,
                'status' => $jobUser->status,
                // UPDATED: Return both date and time components
                'start_date' => $jobUser->start_date ? $jobUser->start_date->format('Y-m-d') : null,
                'start_time' => $jobUser->start_time ? $jobUser->start_time->format('H:i') : null,
                'end_date' => $jobUser->end_date ? $jobUser->end_date->format('Y-m-d') : null,
                'end_time' => $jobUser->end_time ? $jobUser->end_time->format('H:i') : null,
                // UPDATED: Include formatted duration
                'formatted_duration' => $jobUser->formatted_duration ?? 'Not set',
                'duration_real_days' => $jobUser->duration ?? 0,
                // ADDED: Include datetime objects for calculations
                'start_datetime' => ($jobUser->start_date && $jobUser->start_time)
                    ? $jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s')
                    : null,
                'end_datetime' => ($jobUser->end_date && $jobUser->end_time)
                    ? $jobUser->end_date->format('Y-m-d') . ' ' . $jobUser->end_time->format('H:i:s')
                    : null,
            ];
        }),
        'extension_requests' => $task->taskExtensionRequests->map(function($request) {
            return [
                'requested_end_date' => $request->requested_end_date->format('M d, Y'),
                'requested_end_time' => $request->requested_end_time ? $request->requested_end_time->format('H:i') : null,
                'reason' => $request->reason,
                'status' => $request->status,
                'formatted_extension' => $request->formatted_extension ?? $request->formatted_extension_days
            ];
        })
    ]);
}
}
