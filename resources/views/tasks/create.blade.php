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
                                {{-- start time --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="start_time">Start Time</label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}">
                                        @error('start_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                            </div>
                            <div class="row">
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
                            <!-- End Time -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="end_time">End Time</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                            <!-- Enhanced User Selection -->
                            <div class="form-group mb-4">
                                <label>Assign Users <span class="text-danger">*</span></label>

                                <!-- Search Input -->
                                <div class="user-search-container mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control"
                                               id="user-search"
                                               placeholder="Search users by name or userRole">
                                    </div>
                                </div>

                                <!-- Selected Users Display -->
                                <div class="selected-users-container mb-3" id="selected-users" style="display: none;">
                                    <label class="form-label text-muted small">Selected Users:</label>
                                    <div class="selected-users-list" id="selected-users-list"></div>
                                </div>

                                <!-- Available Users List -->
                                <div class="available-users-container">
                                    <div class="user-list" id="user-list">
                                        @foreach($users->take(5) as $user)
                                            <div class="user-item"
                                                 data-id="{{ $user->id }}"

                                                 data-name="{{ strtolower($user->name ?? 'n/a') }}"
                                                 data-userRole="{{ strtolower($user->userRole->name ?? 'no role') }}">
                                                <div class="user-card">
                                                    <div class="user-info">
                                                        <div class="user-name">{{ $user->name ?? 'N/A' }}</div>
                                                        <div class="user-userRole">{{ $user->userRole->name ?? 'No Role' }}</div>
                                                    </div>
                                                    <div class="user-action">
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-user">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div id="remaining-users" style="display: none;">
                                            @foreach($users->skip(5) as $user)
                                                <div class="user-item"
                                                     data-id="{{ $user->id }}"
                                                     data-name="{{ strtolower($user->name ?? 'n/a') }}"
                                                     data-userRole="{{ strtolower($user->userRole->name ?? 'no role') }}">
                                                    <div class="user-card">
                                                        <div class="user-info">
                                                            <div class="user-name">{{ $user->name ?? 'N/A' }}</div>
                                                            <div class="user-userRole">{{ $user->userRole->name ?? 'No Role' }}</div>
                                                        </div>
                                                        <div class="user-action">
                                                            <button type="button" class="btn btn-sm btn-outline-primary add-user">
                                                                <i class="fas fa-plus"></i> Add
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($users->count() > 5)
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-link" id="show-more-btn">
                                                    Show More Users
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Hidden inputs for selected users -->
                                <div id="hidden-user-inputs"></div>

                                @error('user_ids')
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
.user-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.3rem;
    background: #f8f9fa;
}

.user-item {
    margin-bottom: 0.2rem;
}

.user-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.15rem;
    transition: all 0.2s ease;
}

.user-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 500;
    color: #333;
    margin-bottom: 0.25rem;
}

.user-department {
    font-size: 0.875rem;
    color: #6c757d;
    font-style: italic;
}

.user-action {
    margin-left: 1rem;
}

.selected-users-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.selected-user-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: #0d6efd;
    color: white;
    border-radius: 1.5rem;
    font-size: 0.875rem;
    gap: 0.5rem;
}

.selected-user-tag .remove-user {
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

.selected-user-tag .remove-user:hover {
    background: rgba(255,255,255,0.2);
}

.user-item.hidden {
    display: none;
}

.no-users-message {
    text-align: center;
    color: #6c757d;
    padding: 2rem;
    font-style: italic;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let selectedUsers = [];

    // Show more users functionality - Fixed button ID
    $('#show-more-btn').on('click', function() {
        $('#remaining-users').show();
        $(this).parent().hide();
    });

    // Fixed Search functionality with proper debouncing
    let searchTimeout;
    $('#user-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Add debouncing to prevent too many searches
        searchTimeout = setTimeout(function() {
            $('.user-item').each(function() {
                const userName = $(this).data('name') || '';
                const userUserRole = $(this).data('userrole') || '';

                // Check if user is already selected
                const userId = $(this).data('id').toString();
                const isSelected = selectedUsers.includes(userId);

                if (!isSelected && (userName.includes(searchTerm) || userUserRole.includes(searchTerm))) {
                    $(this).removeClass('hidden');
                } else if (!isSelected) {
                    $(this).addClass('hidden');
                }
            });

            // Show "no results" message if no users visible
            const visibleUsers = $('.user-item:not(.hidden)').length;
            $('.no-users-message').remove();

            if (visibleUsers === 0 && searchTerm.length > 0) {
                $('#user-list').append('<div class="no-users-message">No users found matching your search.</div>');
            }
        }, 300); // 300ms debounce
    });

    // Add user
    $(document).on('click', '.add-user', function() {
        const userItem = $(this).closest('.user-item');
        const userId = userItem.data('id');
        const userName = userItem.find('.user-name').text();
        const userUserRole = userItem.find('.user-userRole').text();

        addUserToSelected(userId, userName, userUserRole);
    });

    // Remove user
    $(document).on('click', '.remove-user', function() {
        const userId = $(this).data('id');
        removeUserFromSelected(userId);
    });

    function addUserToSelected(userId, userName, userUserRole) {
        // Check if already selected
        if (selectedUsers.includes(userId.toString())) {
            return;
        }

        selectedUsers.push(userId.toString());

        // Create selected user tag
        const userTag = `
            <div class="selected-user-tag" data-id="${userId}">
                <span>${userName}</span>
                <button type="button" class="remove-user" data-id="${userId}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        $('#selected-users-list').append(userTag);
        $('#selected-users').show();

        // Hide from available list
        $(`.user-item[data-id="${userId}"]`).addClass('hidden');

        // Add hidden input
        $('#hidden-user-inputs').append(`<input type="hidden" name="user_ids[]" value="${userId}">`);

        updateUserSearch();
    }

    function removeUserFromSelected(userId) {
        selectedUsers = selectedUsers.filter(id => id !== userId.toString());

        // Remove tag
        $(`.selected-user-tag[data-id="${userId}"]`).remove();

        // Show in available list
        $(`.user-item[data-id="${userId}"]`).removeClass('hidden');

        // Remove hidden input
        $(`input[name="user_ids[]"][value="${userId}"]`).remove();

        // Hide selected section if no users selected
        if (selectedUsers.length === 0) {
            $('#selected-users').hide();
        }

        updateUserSearch();
    }

    function updateUserSearch() {
        // Trigger search to refresh the list
        $('#user-search').trigger('input');
    }

    // Fix time input popup closing issue
    $('input[type="time"]').on('blur', function() {
        // Force close any open time picker by removing focus
        $(this).blur();

        // Additional fix for some browsers that keep the picker open
        setTimeout(() => {
            if (document.activeElement === this) {
                this.blur();
            }
        }, 100);
    });

    // Handle time input change event to close picker
    $('input[type="time"]').on('change', function() {
        // Force blur to close picker
        $(this).blur();
    });

    // Handle click outside time input to close picker
    $(document).on('click', function(e) {
        if (!$(e.target).is('input[type="time"]')) {
            $('input[type="time"]').blur();
        }
    });

    // Form validation
    $('#task-create-form').on('submit', function(e) {
        if (selectedUsers.length === 0) {
            e.preventDefault();
            alert('Please select at least one user for this task.');
            return false;
        }
    });
});
</script>
@endsection
