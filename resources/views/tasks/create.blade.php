


@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Create Task</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
<form method="POST" action="{{ route('jobs.tasks.store', $job) }}">
    @csrf
    <div>
        <label for="task">Task Name</label>
        <input type="text" name="task" required value="{{ old('task') }}">
    </div>
    <div>
        <label for="description">Description</label>
        <textarea name="description">{{ old('description') }}</textarea>
    </div>
    <div>
        <label for="cash_issued_date">Cash Issued Date</label>
        <input type="date" name="cash_issued_date" value="{{ old('cash_issued_date') }}">
    </div>
    <div>
        <label for="start_time">Start Time</label>
        <input type="time" name="start_time" value="{{ old('start_time') }}">
    </div>
    <div>
        <label for="end_time">End Time</label>
        <input type="time" name="end_time" value="{{ old('end_time') }}">
    </div>
    <div>
        <label for="status">Status</label>
        <select name="status" required>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>
    <div>
        <label for="pending_reason">Pending Reason</label>
        <textarea name="pending_reason">{{ old('pending_reason') }}</textarea>
    </div>
    <div>
        <label for="target_date">Target Date</label>
        <input type="date" name="target_date" value="{{ old('target_date') }}">
    </div>
    <div>
        <label for="employee_ids">Assign Employees</label>
        <select name="employee_ids[]" multiple required>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}">
    </div>
    <div>
        <label for="end_date">End Date</label>
        <input type="date" name="end_date" value="{{ old('end_date') }}">
    </div>
    <div>
        <label for="notes">Notes</label>
        <textarea name="notes">{{ old('notes') }}</textarea>
    </div>
    <div>
        <label><input type="checkbox" name="is_active" checked> Active</label>
    </div>
    <button type="submit">Create Task</button>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
