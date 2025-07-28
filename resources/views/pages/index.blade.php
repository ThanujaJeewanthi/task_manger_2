@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Pages</span>
                            </div>
                             
                                                 @if (App\Helpers\UserRoleHelper::hasPermission('2.6'))
                            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Page
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
                                        <th>Code</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pages as $page)
                                        <tr>
                                            <td>{{ $page->id }}</td>
                                            <td>{{ $page->name }}</td>
                                            <td><code>{{ $page->code }}</code></td>
                                            <td>{{ $page->category->name }}</td>
                                            <td>
                                                <span class="badge {{ $page->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $page->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $page->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                    @if (App\Helpers\UserRoleHelper::hasPermission('2.7'))
                                                <a href="{{ route('admin.pages.edit', $page->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                   @if (App\Helpers\UserRoleHelper::hasPermission('2.8'))

                                                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this page?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($pages->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center">No pages found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $pages->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
