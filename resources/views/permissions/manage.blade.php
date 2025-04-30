@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Permissions</h4>
                </div>
                <div>
                    <input type="text" id="search-input" class="form-control" placeholder="Search pages or categories...">
                    <div id="search-results" class="list-group mt-2"></div>

                    <script>
                        document.getElementById('search-input').addEventListener('keyup', function () {
                            let query = this.value;

                            if (query.length < 2) {
                                document.getElementById('search-results').innerHTML = '';
                                return;
                            }

                            fetch(`/admin/pages/search?q=${query}`)
                                .then(response => response.json())
                                .then(data => {
                                    let resultBox = document.getElementById('search-results');
                                    resultBox.innerHTML = '';

                                    if (data.length === 0) {
                                        resultBox.innerHTML = '<div class="list-group-item">No results found.</div>';
                                    }

                                    data.forEach(item => {
                                        let link = document.createElement('a');
                                        link.href = '#'; // Using JavaScript navigation instead
                                        link.className = 'list-group-item list-group-item-action';
                                        link.innerHTML = `<strong>${item.name}</strong> <br><small>in ${item.page_category.name}</small>`;

                                        // Store data attributes for navigation
                                        link.dataset.pageId = item.id;
                                        link.dataset.categoryId = item.page_category_id;
                                        link.dataset.categoryName = item.page_category.name;
                                        link.dataset.pageName = item.name;

                                        link.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            navigateToPage(item.id, item.page_category_id);
                                        });

                                        resultBox.appendChild(link);
                                    });
                                });
                        });

                        function navigateToPage(pageId, categoryId) {
                            // First, find which role tab is currently active or select the first one
                            let activeRoleTab = document.querySelector('.nav-link.active');
                            let roleId = activeRoleTab.id.replace('role-', '').replace('-tab', '');

                            // Activate the tab
                            let tabElement = document.getElementById(`role-${roleId}-tab`);
                            if (tabElement) {
                                tabElement.click();
                            }

                            // Find the row with matching page ID
                            let targetRow = document.querySelector(`tr[data-page-id="${pageId}"]`);

                            if (targetRow) {
                                // Remove highlighting from any previously highlighted row
                                document.querySelectorAll('tr.highlight-row').forEach(row => {
                                    row.classList.remove('highlight-row');
                                });

                                // Highlight the row
                                targetRow.classList.add('highlight-row');

                                // Scroll to the row
                                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

                                // Clear the search
                                document.getElementById('search-input').value = '';
                                document.getElementById('search-results').innerHTML = '';
                            }
                        }
                    </script>
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
                                                    <tr data-page-id="{{ $page->id }}" data-category-id="{{ $category->id }}">
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
                                                                <label class="form-check-label status-label"
                                                                       for="switch-{{ $page->id }}-{{ $tabRole->id }}"
                                                                       data-allowed="{{ $currentStatus === 'allow' ? '1' : '0' }}">
                                                                    {{ $currentStatus === 'allow' ? 'Allowed' : 'Disallowed' }}
                                                                </label>
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

    /* Highlight styling for the selected row */
    tr.highlight-row {
        background-color: #fff3cd;
        transition: background-color 0.5s ease;
        animation: highlight-fade 2s ease;
    }

    @keyframes highlight-fade {
        0% { background-color: #ffe066; }
        100% { background-color: #fff3cd; }
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

        // Check if there's a page to navigate to from URL hash
        if (window.location.hash) {
            try {
                const hashParams = window.location.hash.substring(1).split('-');
                if (hashParams.length === 2) {
                    const pageId = hashParams[0];
                    const categoryId = hashParams[1];

                    // Add a small delay to ensure DOM is fully loaded
                    setTimeout(() => {
                        navigateToPage(pageId, categoryId);
                    }, 300);
                }
            } catch (e) {
                console.error('Error parsing hash parameters:', e);
            }
        }
    });

    // Function that can be called from outside the DOMContentLoaded event
    function navigateToPage(pageId, categoryId) {
        // Find which role tab is currently active or select the first one
        let activeRoleTab = document.querySelector('.nav-link.active');
        let roleId = activeRoleTab.id.replace('role-', '').replace('-tab', '');

        // Activate the tab
        let tabElement = document.getElementById(`role-${roleId}-tab`);
        if (tabElement) {
            // Use Bootstrap's tab API
            bootstrap.Tab.getOrCreateInstance(tabElement).show();
        }

        // Find the row with matching page ID
        let targetRow = document.querySelector(`tr[data-page-id="${pageId}"]`);

        if (targetRow) {
            // Remove highlighting from any previously highlighted row
            document.querySelectorAll('tr.highlight-row').forEach(row => {
                row.classList.remove('highlight-row');
            });

            // Highlight the row
            targetRow.classList.add('highlight-row');

            // Scroll to the row
            setTimeout(() => {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300); // Small delay to ensure tab content is visible

            // Update URL hash for bookmarking
            window.location.hash = `${pageId}-${categoryId}`;
        }
    }
</script>
@endpush
