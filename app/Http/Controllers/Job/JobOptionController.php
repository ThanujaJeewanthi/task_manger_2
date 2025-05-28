<?php

namespace App\Http\Controllers\Job;

use App\Models\JobOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class JobOptionController extends Controller
{
    public function index()
    {
        $jobOptions = JobOption::where('active', true)->paginate(10);
        return view( 'job-options.index', compact('jobOptions'));
    }

    public function create()
    {
        return view( 'job-options.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'option_type' => 'required|in:text,number,date,select,checkbox,file',

        ]);

        JobOption::create([
            'name' => $request->name,
            'description' => $request->description,
            'option_type' => $request->option_type,
            // 'options_json' => $request->options_json,
            'required' => $request->has('required'),
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route( 'job-options.index')->with('success', 'Job Option created successfully.');
    }

    // public function show(JobOption $jobOption)
    // {
    //     return view( 'job-options.show', compact('jobOption'));
    // }

    public function edit(JobOption $jobOption)
    {
        return view( 'job-options.edit', compact('jobOption'));
    }

    public function update(Request $request, JobOption $jobOption)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'option_type' => 'required|in:text,number,date,select,checkbox,file',
            // 'options_json' => 'nullable|json',
            // 'required' => 'boolean'
        ]);

        $jobOption->update([
            'name' => $request->name,
            'description' => $request->description,
            'option_type' => $request->option_type,
            // 'options_json' => $request->options_json,
            'required' => $request->has('required'),
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route( 'job-options.index')->with('success', 'Job Option updated successfully.');
    }

    public function destroy(JobOption $jobOption)
    {
        $jobOption->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route( 'job-options.index')->with('success', 'Job Option updated successfully.');
    }
}
