@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
             <div class="d-component-title">
                    <span>Manage Permissions for {{ $role->name }}</span>
                    </div>

            <h6 class="text-muted">Role ID: {{ $role->id }}</h6>
        </div>
        <form id="search-form">
            <div class="input-group me-3">
                <span class="input-group-text" id="search-icon">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="search-input" class="form-control" placeholder="Search pages or categories..." aria-describedby="search-icon">
            </div>
        </form>

 <div id="search-results" class="list-group mt-2"></div>




        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                @foreach($pageCategories as $tabPageCategory)
                    <li class="nav-item">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="category-{{ $tabPageCategory->id }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#category-{{ $tabPageCategory->id }}">
                            {{ $tabPageCategory->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mt-3">
                @foreach($pageCategories as $tabPageCategory)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" style="background-color: #ffffff;"
                         id="category-{{ $tabPageCategory->id }}"
                         role="tabpanel">

                        <form action="{{ route('admin.permissions.update', ['roleId' => $role->id]) }}"
                              method="POST" class="permission-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="page_category_id" value="{{ $tabPageCategory->id }}">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Page Name</th>
                                        <th>Allowed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tabPageCategory->pages as $page)
                                    @php
                                        $detail = $page->userRoleDetails->where('user_role_id', $role->id)->first();
                                        $isAllowed = $detail && $detail->status === 'allow';
                                    @endphp
                                        <tr data-page-id="{{ $page->id }}">
                                            <td>{{ $page->name }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox"
                                                           class="form-check-input permission-toggle"
                                                           id="permission-{{ $role->id }}-{{ $page->id }}"
                                                           name="permissions[{{ $page->id }}]"
                                                           value="allow"
                                                           data-role-id="{{ $role->id }}"
                                                           data-page-id="{{ $page->id }}"
                                                           {{ $isAllowed ? 'checked' : '' }}>

                                                    <label class="form-check-label"
                                                           for="permission-{{ $role->id }}-{{ $page->id }}">
                                                        {{ $isAllowed ? 'Allowed' : 'Disallowed' }}
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary mt-3">
                                Update Permissions for {{ $tabPageCategory->name }}
                            </button>

                        </form>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<script>

$(document).ready(function() {
    // Prevent default form submission
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // Search on keyup with debounce
    let searchTimer;
    $('#search-input').on('keyup', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val().trim();

        // Hide results if search term is empty
        if (searchTerm === '') {
            $('#search-results').empty().hide();
            return;
        }

        // Set a timer to delay the search for better performance
        searchTimer = setTimeout(function() {
            performSearch();
        }, 300);
    });

    // Function to perform the AJAX search
    function performSearch() {
        const searchTerm = $('#search-input').val().trim();

        if (searchTerm === '') {
            $('#search-results').empty().hide();
            return;
        }

        // Show a loading indicator
        $('#search-results').html('<div class="list-group-item">Searching...</div>').show();

        // AJAX request to server
        $.ajax({
            url: '{{ route("admin.permissions.search") }}',
            type: 'POST',
            data: {
                search: searchTerm,
                role_id: '{{ $role->id }}',
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                displaySearchResults(response, searchTerm);
            },
            error: function(xhr) {
                $('#search-results').html('<div class="list-group-item text-danger">Error: orm search</div>');
                console.error('Search error:', xhr.responseText);
            }
        });
    }

    // Function to display search results
    function displaySearchResults(results, searchTerm) {
        const resultsContainer = $('#search-results');
        resultsContainer.empty();

        if (results.length === 0) {
            resultsContainer.html('<div class="list-group-item">No results found</div>');
            return;
        }

        // Group results by category
        const groupedResults = {};
        results.forEach(function(result) {
            if (!groupedResults[result.category_name]) {
                groupedResults[result.category_name] = [];
            }
            groupedResults[result.category_name].push(result);
        });

        // Display results grouped by category
        Object.keys(groupedResults).forEach(function(categoryName) {
            const categoryResults = groupedResults[categoryName];

            // Add category header
            resultsContainer.append(`
                <div class="list-group-item list-group-item-secondary">
                    <strong>${categoryName}</strong>
                </div>
            `);

            // Add pages within category
            categoryResults.forEach(function(result) {
                // Highlight the matching text
                const highlightedName = highlightText(result.page_name, searchTerm);

                resultsContainer.append(`
                    <a href="#" class="list-group-item list-group-item-action search-result"
                       data-category-id="${result.category_id}"
                       data-page-id="${result.page_id}">
                        ${highlightedName}
                        <span class="badge ${result.is_allowed ? 'bg-success' : 'bg-danger'} float-end">
                            ${result.is_allowed ? 'Allowed' : 'Disallowed'}
                        </span>
                    </a>
                `);
            });
        });

        resultsContainer.show();
    }

    // Function to highlight search term in text
    function highlightText(text, searchTerm) {
        if (!searchTerm) return text;

        const regex = new RegExp('(' + escapeRegExp(searchTerm) + ')', 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Helper function to escape special regex characters
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Handle click on search result
    $(document).on('click', '.search-result', function(e) {
        e.preventDefault();

        const categoryId = $(this).data('category-id');
        const pageId = $(this).data('page-id');

        // Switch to the appropriate tab
        $(`#category-${categoryId}-tab`).tab('show');

        // Find and highlight the row
        const targetRow = $(`#category-${categoryId} tr[data-page-id="${pageId}"]`);

        // Remove any existing highlights
        $('tr.highlight-row').removeClass('highlight-row');

        // Highlight the target row
        targetRow.addClass('highlight-row');

        // Scroll to the row
        $('html, body').animate({
            scrollTop: targetRow.offset().top - 100
        }, 500, function() {
        // After animation completes, ensure scrolling is fully enabled
        $('html, body').css({
            'overflow': 'auto',
            'height': 'auto'
        });
        });

        // Hide search results
        $('#search-results').hide();
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-form, #search-results').length) {
            $('#search-results').hide();
        }
    });

    // Toggle permission label text when checkbox changes
    $('.permission-toggle').on('change', function() {
        const label = $(this).next('label');
        if ($(this).is(':checked')) {
            label.text('Allowed');
        } else {
            label.text('Disallowed');
        }
    });
});
</script>


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

    /* Search styles */
    .search-container {
        position: relative;
        margin-bottom: 1rem;
    }

    #search-input {
        padding-left: 2.5rem;
        border-radius: 1.5rem;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 0.75rem;
        color: #6c757d;
    }

    #search-results {
        position: absolute;
        width: 100%;
        max-height: 350px;
        overflow-y: auto;
        z-index: 1000;
        border-radius: 0.25rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    mark {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.2rem;
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



