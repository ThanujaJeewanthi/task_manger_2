@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:600px;">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Extend Task for Job: {{ $job->id }}</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mt-3">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('jobs.extend-task.store', $job) }}" method="POST">
                        @csrf

                        <div class="d-component-container">
                            <!-- Task Selection -->
                            <div class="form-group mb-4">
                                <label for="task_id">Select Task to Extend</label>
                                <select class="form-control @error('task_id') is-invalid @enderror" id="task_id" name="task_id" required>
                                    <option value="">Select Task</option>
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->id }}">{{ $task->task }} (End Date: {{ $task->jobEmployees->first()->end_date ? $task->jobEmployees->first()->end_date->format('Y-m-d') : 'N/A' }})</option>
                                    @endforeach
                                </select>
                                @error('task_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- New End Date -->
                            <div class="form-group mb-4">
                                <label for="new_end_date">New End Date</label>
                                <input type="date" class="form-control @error('new_end_date') is-invalid @enderror" id="new_end_date" name="new_end_date" value="{{ old('new_end_date') }}" required>
                                @error('new_end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create New Job with Extended Task</button>
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection