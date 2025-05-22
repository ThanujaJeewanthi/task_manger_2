<?php

namespace App\Http\Controllers\Job;

use App\Models\JobType;
use App\Models\JobOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobTypeController extends Controller
{
    public function index()
    {
        $jobTypes = JobType::with('jobOptions')->where('active', true)->paginate(10);
        return view( 'job-types.index', compact('jobTypes'));
    }

    public function create()
    {
        $jobOptions = JobOption::where('active', true)->get();
        return view( 'job-types.create', compact('jobOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'job_options' => 'array',
            'job_options.*' => 'exists:job_options,id',
            'sort_orders' => 'array',
            'sort_orders.*' => 'integer|min:0'
        ]);

        $jobType = JobType::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        // Attach job options with sort order
        if ($request->has('job_options')) {
            foreach ($request->job_options as $index => $optionId) {
                $jobType->jobOptions()->attach($optionId, [
                    'sort_order' => $request->sort_orders[$index] ?? 0,
                    'created_by' => Auth::id()
                ]);
            }
        }

        return redirect()->route( 'job-types.index')->with('success', 'Job Type created successfully.');
    }

    public function show(JobType $jobType)
    {
        $jobType->load('jobOptions');
        return view( 'job-types.show', compact('jobType'));
    }

    public function edit(JobType $jobType)
    {
        $jobType->load('jobOptions');
        $jobOptions = JobOption::where('active', true)->get();
        return view( 'job-types.edit', compact('jobType', 'jobOptions'));
    }

    public function update(Request $request, JobType $jobType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'job_options' => 'array',
            'job_options.*' => 'exists:job_options,id',
            'sort_orders' => 'array',
            'sort_orders.*' => 'integer|min:0'
        ]);

        $jobType->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        // Sync job options with sort order
        $syncData = [];
        if ($request->has('job_options')) {
            foreach ($request->job_options as $index => $optionId) {
                $syncData[$optionId] = [
                    'sort_order' => $request->sort_orders[$index] ?? 0,
                    'updated_by' => Auth::id()
                ];
            }
        }
        $jobType->jobOptions()->sync($syncData);

        return redirect()->route( 'job-types.index')->with('success', 'Job Type updated successfully.');
    }

    public function destroy(JobType $jobType)
    {
        $jobType->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route( 'job-types.index')->with('success', 'Job Type deleted successfully.');
    }
}
