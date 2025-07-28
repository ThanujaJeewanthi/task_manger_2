@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Suppliers</span>
                        </div>
                         @if (App\Helpers\UserRoleHelper::hasPermission('8.2'))
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-compact">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $supplier->id }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->description ?? 'N/A' }}</td>
                                        <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
                                        <td>{{ $supplier->email ?? 'N/A' }}</td>
                                        <td>{{ $supplier->phone ?? 'N/A' }}</td>
                                        <td>{{ $supplier->address ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $supplier->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $supplier->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $supplier->created_at->format('Y-m-d H:i') }}</td>
                                        <td>

                                             @if (App\Helpers\UserRoleHelper::hasPermission('8.3'))
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                             @endif
                                             @if (App\Helpers\UserRoleHelper::hasPermission('8.4'))
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No suppliers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
