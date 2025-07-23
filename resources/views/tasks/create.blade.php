@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:800px;">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Create Task for Job: {{ $job->id }}</span>
                        </div>
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Job
                        </a>
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

                            <!-- Assignment Type Selection -->
                            <div class="form-group mb-4">
                                <label for="assignment_type">Assignment Type</label>
                                <select class="form-control" id="assignment_type" name="assignment_type" onchange="toggleAssignmentFields()">
                                    <option value="user" {{ old('assignment_type', 'user') == 'user' ? 'selected' : '' }}>Assign Users (Recommended)</option>
                                    <option value="employee" {{ old('assignment_type') == 'employee' ? 'selected' : '' }}>Assign Employees (Legacy)</option>
                                </select>
                            </div>

                            <!-- User Assignment Section -->
                            <div class="form-group mb-4" id="user_assignment_section">
                                <label for="user_search">Assign Users</label>
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="user_search" placeholder="Search users by name or role..." onkeyup="filterUsers()">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        <div id="user_list">
                                            @foreach($assignableUsers as $user)
                                                <div class="user-item border rounded p-2 mb-2" data-user-id="{{ $user->id }}" data-user-name="{{ strtolower($user->name) }}" data-user-role="{{ strtolower($user->userRole->name ?? '') }}">
                                                    <div class="form-check">
                                                        <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ collect(old('user_ids', []))->contains($user->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label w-100" for="user_{{ $user->id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <strong>{{ $user->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $user->email ?? 'No email' }}</small>
                                                                </div>
                                                                <div>
                                                                    @php
                                                                        $roleName = $user->userRole->name ?? 'No Role';
                                                                        $badgeClass = \App\Helpers\UserRoleHelper::getRoleBadgeClass($roleName);
                                                                    @endphp
                                                                    <span class="badge {{ $badgeClass }}">{{ $roleName }}</span>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-footer py-2">
                                        <small class="text-muted">
                                            <span id="selected_users_count">0</span> user(s) selected
                                            • All roles except Admin and Super Admin can be assigned
                                        </small>
                                    </div>
                                </div>
                                @error('user_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Employee Assignment Section (Legacy) -->
                            <div class="form-group mb-4" id="employee_assignment_section" style="display: none;">
                                <label for="employee_search">Assign Employees (Legacy)</label>
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="employee_search" placeholder="Search employees..." onkeyup="filterEmployees()">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        <div id="employee_list">
                                            @foreach($employees as $employee)
                                                <div class="employee-item border rounded p-2 mb-2" data-employee-name="{{ strtolower($employee->name) }}">
                                                    <div class="form-check">
                                                        <input class="form-check-input employee-checkbox" type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" id="employee_{{ $employee->id }}" {{ collect(old('employee_ids', []))->contains($employee->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label w-100" for="employee_{{ $employee->id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <strong>{{ $employee->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $employee->job_title ?? 'No job title' }} • {{ $employee->department ?? 'No department' }}</small>
                                                                </div>
                                                                <span class="badge badge-primary">Employee</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="card-footer py-2">
                                        <small class="text-muted">
                                            <span id="selected_employees_count">0</span> employee(s) selected
                                            • Legacy employee assignment. Use user assignment for better role management.
                                        </small>
                                    </div>
                                </div>
                                @error('employee_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

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
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional task description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
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

                            <!-- Notes -->
                            <div class="form-group mb-4">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional notes for the assignment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Task
                                </button>
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ml-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle between user and employee assignment sections
function toggleAssignmentFields() {
    const assignmentType = document.getElementById('assignment_type').value;
    const userSection = document.getElementById('user_assignment_section');
    const employeeSection = document.getElementById('employee_assignment_section');
    
    if (assignmentType === 'user') {
        userSection.style.display = 'block';
        employeeSection.style.display = 'none';
        // Clear employee selections
        document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = false);
        updateSelectedEmployeesCount();
    } else {
        userSection.style.display = 'none';
        employeeSection.style.display = 'block';
        // Clear user selections
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        updateSelectedUsersCount();
    }
}

// Filter users based on search input
function filterUsers() {
    const searchTerm = document.getElementById('user_search').value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');
    
    userItems.forEach(item => {
        const userName = item.getAttribute('data-user-name');
        const userRole = item.getAttribute('data-user-role');
        
        if (userName.includes(searchTerm) || userRole.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Filter employees based on search input
function filterEmployees() {
    const searchTerm = document.getElementById('employee_search').value.toLowerCase();
    const employeeItems = document.querySelectorAll('.employee-item');
    
    employeeItems.forEach(item => {
        const employeeName = item.getAttribute('data-employee-name');
        
        if (employeeName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Update selected users count
function updateSelectedUsersCount() {
    const checkedUsers = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('selected_users_count').textContent = checkedUsers;
}

// Update selected employees count
function updateSelectedEmployeesCount() {
    const checkedEmployees = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('selected_employees_count').textContent = checkedEmployees;
}

// Initialize event listeners when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize assignment type
    toggleAssignmentFields();
    
    // Update counts initially
    updateSelectedUsersCount();
    updateSelectedEmployeesCount();
    
    // Add event listeners for checkbox changes
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedUsersCount);
    });
    
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedEmployeesCount);
    });
    
    // Date validation - end date should be after start date
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        if (this.value) {
            endDate.min = this.value;
        }
    });
    
    endDate.addEventListener('change', function() {
        if (startDate.value && this.value && this.value < startDate.value) {
            alert('End date cannot be before start date');
            this.value = '';
        }
    });
});
</script>

<style>
.user-item:hover, .employee-item:hover {
    background-color: #f8f9fa;
}

.user-item .form-check-input:checked + .form-check-label {
    background-color: #e3f2fd;
}

.employee-item .form-check-input:checked + .form-check-label {
    background-color: #e8f5e8;
}

.badge-primary { background-color: #007bff; }
.badge-info { background-color: #17a2b8; }
.badge-success { background-color: #28a745; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; }
.badge-dark { background-color: #343a40; }
.badge-secondary { background-color: #6c757d; }
</style>
@endsection