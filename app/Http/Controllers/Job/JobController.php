<?php

namespace App\Http\Controllers\Job;

use App\Models\Job;
use App\Models\JobType;
use App\Models\Client;
use App\Models\Equipment;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

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
        $jobTypes = JobType::where('active', true)->get();
        $clients = Client::where('company_id', $companyId)->where('active', true)->get();
        $equipments = Equipment::where('company_id', $companyId)->where('active', true)->get();

        return view('jobs.create', compact('jobTypes', 'clients', 'equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ]);

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
        $jobTypes = JobType::where('active', true)->get();
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

    public function assignEmployee(Request $request, Job $job)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'custom_task' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string'
        ]);

        $job->jobEmployees()->create([
            'employee_id' => $request->employee_id,
            'task_id' => $request->task_id,
            'custom_task' => $request->custom_task,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_in_days' => $request->start_date && $request->end_date ?
                \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 : null,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Employee assigned to job successfully.');
    }
}
