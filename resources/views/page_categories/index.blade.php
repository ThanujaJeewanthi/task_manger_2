@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="d-component-title">
                                <span>Page Categories</span>
                            </div>
    @if (App\Helpers\UserRoleHelper::hasPermission('2.2'))
                            <a href="{{ route('admin.page-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Category
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive table-compact">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pageCategories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <span class="badge {{ $category->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $category->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                    @if (App\Helpers\UserRoleHelper::hasPermission('2.3'))
                                                <a href="{{ route('admin.page-categories.edit', $category->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                    @if (App\Helpers\UserRoleHelper::hasPermission('2.4'))

                                                <form action="{{ route('admin.page-categories.destroy', $category->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($pageCategories->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center">No page categories found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $pageCategories->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
