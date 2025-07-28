@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:600px;">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Create Task for Job: {{ $job->id }}</span>
                        </div>
                        @if(App\Helpers\UserRoleHelper::hasPermission('11.12'))
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Job
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('jobs.tasks.store', $job) }}" method="POST" id="task-create-form">
                        @csrf

                        <div class="d-component-container">
                            <!-- Task Name -->
                            <div class="form-group mb-4">
                                <label for="task">Task Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('task') is-invalid @enderror" id="task" name="task" value="{{ old('task') }}" required>
                                @error('task')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-4">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Start Date -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Employee Selection -->
                            <div class="form-group mb-4">
                                <label>Assign Employees <span class="text-danger">*</span></label>

                                <!-- Search Input -->
                                <div class="employee-search-container mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control"
                                               id="employee-search"
                                               placeholder="Search employees by name or department...">
                                    </div>
                                </div>

                                <!-- Selected Employees Display -->
                                <div class="selected-employees-container mb-3" id="selected-employees" style="display: none;">
                                    <label class="form-label text-muted small">Selected Employees:</label>
                                    <div class="selected-employees-list" id="selected-employees-list"></div>
                                </div>

                                <!-- Available Employees List -->
                                <div class="available-employees-container">
                                    <div class="employee-list" id="employee-list">
                                        @foreach($employees->take(5) as $employee)
                                            <div class="employee-item"
                                                 data-id="{{ $employee->id }}"
                                                 data-name="{{ strtolower($employee->name ?? 'n/a') }}"
                                                 data-department="{{ strtolower($employee->department ?? 'no department') }}">
                                                <div class="employee-card">
                                                    <div class="employee-info">
                                                        <div class="employee-name">{{ $employee->name ?? 'N/A' }}</div>
                                                        <div class="employee-department">{{ $employee->department ?? 'No Department' }}</div>
                                                    </div>
                                                    <div class="employee-action">
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-employee">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div id="remaining-employees" style="display: none;">
                                            @foreach($employees->skip(5) as $employee)
                                                <div class="employee-item"
                                                     data-id="{{ $employee->id }}"
                                                     data-name="{{ strtolower($employee->name ?? 'n/a') }}"
                                                     data-department="{{ strtolower($employee->department ?? 'no department') }}">
                                                    <div class="employee-card">
                                                        <div class="employee-info">
                                                            <div class="employee-name">{{ $employee->name ?? 'N/A' }}</div>
                                                            <div class="employee-department">{{ $employee->department ?? 'No Department' }}</div>
                                                        </div>
                                                        <div class="employee-action">
                                                            <button type="button" class="btn btn-sm btn-outline-primary add-employee">
                                                                <i class="fas fa-plus"></i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($employees->count() > 5)
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-link" id="show-more-btn">
                                                    Show More Employees
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Hidden inputs for selected employees -->
                                <div id="hidden-employee-inputs"></div>

                                @error('employee_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="form-group mb-4">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Active Status Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Task</button>
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.employee-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.3rem;
    background: #f8f9fa;
}

.employee-item {
    margin-bottom: 0.2rem;
}

.employee-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.15rem;
    transition: all 0.2s ease;
}

.employee-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.employee-info {
    flex: 1;
}

.employee-name {
    font-weight: 500;
    color: #333;
    margin-bottom: 0.25rem;
}

.employee-department {
    font-size: 0.875rem;
    color: #6c757d;
    font-style: italic;
}

.employee-action {
    margin-left: 1rem;
}

.selected-employees-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.selected-employee-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: #0d6efd;
    color: white;
    border-radius: 1.5rem;
    font-size: 0.875rem;
    gap: 0.5rem;
}

.selected-employee-tag .remove-employee {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.selected-employee-tag .remove-employee:hover {
    background: rgba(255,255,255,0.2);
}

.employee-item.hidden {
    display: none;
}

.no-employees-message {
    text-align: center;
    color: #6c757d;
    padding: 2rem;
    font-style: italic;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let selectedEmployees = [];

    // Initialize with old values if form was submitted with errors
    @if(old('employee_ids'))
        const oldEmployeeIds = @json(old('employee_ids'));
        oldEmployeeIds.forEach(function(employeeId) {
            const employeeItem = $(`.employee-item[data-id="${employeeId}"]`);
            if (employeeItem.length) {
                addEmployeeToSelected(employeeId, employeeItem.find('.employee-name').text(), employeeItem.find('.employee-department').text());
            }
        });
    @endif

    // Search functionality
    $('#employee-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();

        $('.employee-item').each(function() {
            const employeeName = $(this).data('name');
            const employeeDepartment = $(this).data('department');

            if (employeeName.includes(searchTerm) || employeeDepartment.includes(searchTerm)) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });

        // Show "no results" message if no employees visible
        const visibleEmployees = $('.employee-item:not(.hidden)').length;
        $('.no-employees-message').remove();

        if (visibleEmployees === 0 && searchTerm.length > 0) {
            $('#employee-list').append('<div class="no-employees-message">No employees found matching your search.</div>');
        }
    });

    // Add employee
    $(document).on('click', '.add-employee', function() {
        const employeeItem = $(this).closest('.employee-item');
        const employeeId = employeeItem.data('id');
        const employeeName = employeeItem.find('.employee-name').text();
        const employeeDepartment = employeeItem.find('.employee-department').text();

        addEmployeeToSelected(employeeId, employeeName, employeeDepartment);
    });

    // Remove employee
    $(document).on('click', '.remove-employee', function() {
        const employeeId = $(this).data('id');
        removeEmployeeFromSelected(employeeId);
    });

    function addEmployeeToSelected(employeeId, employeeName, employeeDepartment) {
        // Check if already selected
        if (selectedEmployees.includes(employeeId.toString())) {
            return;
        }

        selectedEmployees.push(employeeId.toString());

        // Create selected employee tag
        const employeeTag = `
            <div class="selected-employee-tag" data-id="${employeeId}">
                <span>${employeeName}</span>
                <button type="button" class="remove-employee" data-id="${employeeId}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        $('#selected-employees-list').append(employeeTag);
        $('#selected-employees').show();

        // Hide from available list
        $(`.employee-item[data-id="${employeeId}"]`).addClass('hidden');

        // Add hidden input
        $('#hidden-employee-inputs').append(`<input type="hidden" name="employee_ids[]" value="${employeeId}">`);

        updateEmployeeSearch();
    }

    function removeEmployeeFromSelected(employeeId) {
        selectedEmployees = selectedEmployees.filter(id => id !== employeeId.toString());

        // Remove tag
        $(`.selected-employee-tag[data-id="${employeeId}"]`).remove();

        // Show in available list
        $(`.employee-item[data-id="${employeeId}"]`).removeClass('hidden');

        // Remove hidden input
        $(`input[name="employee_ids[]"][value="${employeeId}"]`).remove();

        // Hide selected section if no employees selected
        if (selectedEmployees.length === 0) {
            $('#selected-employees').hide();
        }

        updateEmployeeSearch();
    }

    function updateEmployeeSearch() {
        // Trigger search to refresh the list
        $('#employee-search').trigger('input');
    }

    // Form validation
    $('#task-create-form').on('submit', function(e) {
        if (selectedEmployees.length === 0) {
            e.preventDefault();
            alert('Please select at least one employee for this task.');
            return false;
        }
    });
});
</script>
@endsection
