@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Permissions</h4>
                </div>

                <div class="card-body">
                    {{-- @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif --}}

                    <!-- Roles as Tabs -->
                    <ul class="nav nav-tabs" id="rolesTab" role="tablist">
                        @foreach($roles as $tabRole)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="role-{{ $tabRole->id }}-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#role-{{ $tabRole->id }}"
                                        type="button"
                                        role="tab"
                                        aria-controls="role-{{ $tabRole->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $tabRole->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="rolesTabContent">
                        @foreach($roles as $tabRole)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                 id="role-{{ $tabRole->id }}"
                                 role="tabpanel"
                                 aria-labelledby="role-{{ $tabRole->id }}-tab">

                                <form action="{{ route('admin.permissions.update', $tabRole->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('PUT')

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Page</th>
                                                <th>Code</th>
                                                <th>Permission Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pageCategories as $category)
                                                @foreach($category->pages as $page)
                                                    @php
                                                        $permission = $tabRole->userRoleDetails->where('page_id', $page->id)->first();
                                                        $currentStatus = $permission ? $permission->status : 'disallow';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $category->name }}</td>
                                                        <td>{{ $page->name }}</td>
                                                        <td>{{ $page->code }}</td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input permission-toggle"
                                                                       type="checkbox"
                                                                       name="permissions[{{ $page->id }}]"
                                                                       value="allow"
                                                                       {{ $currentStatus === 'allow' ? 'checked' : '' }}
                                                                       id="switch-{{ $page->id }}-{{ $tabRole->id }}"
                                                                       data-page-id="{{ $page->id }}">
                                                                {{-- <label class="form-check-label status-label"
                                                                       for="switch-{{ $page->id }}-{{ $tabRole->id }}"
                                                                       data-allowed="{{ $currentStatus === 'allow' ? '1' : '0' }}">
                                                                    {{ $currentStatus === 'allow' ? 'Allowed' : 'Disallowed' }}
                                                                </label> --}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Permissions for {{ $tabRole->name }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Custom styling for the tabs */
    .nav-tabs .nav-link {
        border-radius: 0.25rem;
        margin-right: 0.25rem;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .nav-tabs .nav-link:hover {
        background-color: #f8f9fa;
    }

    /* Custom styling for the switches */
    .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    .form-check-label {
        margin-left: 0.5rem;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Update toggle switches and labels
    document.querySelectorAll('.permission-toggle').forEach(toggle => {
        const label = toggle.nextElementSibling;

        // Initialize
        updateLabelState(toggle, label);

        // Handle changes
        toggle.addEventListener('change', function() {
            updateLabelState(toggle, label);
        });
    });

    function updateLabelState(toggle, label) {
        const isAllowed = toggle.checked;
        label.textContent = isAllowed ? 'Allowed' : 'Disallowed';
        label.dataset.allowed = isAllowed ? '1' : '0';
    }

    // Form submission handling
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // No need for additional processing - form will submit all checked boxes
        });
    });
});

</script>
@endpush
