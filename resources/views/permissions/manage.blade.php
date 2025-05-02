@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Manage Permissions for {{ $role->name }}</h4>
            <h6 class="text-muted">Role ID: {{ $role->id }}</h6>
        </div>

        <div class="p-3 border-bottom search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="search-input" class="form-control" placeholder="Search pages or categories...">
            <div id="search-results" class="list-group mt-2"></div>
        </div>

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
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
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

