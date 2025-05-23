@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Equipment</span>
                        </div>
                        <a href="{{ route('equipments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Equipment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Model</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Active</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equipments as $equipment)
                                    <tr>
                                        <td>{{ $equipment->id }}</td>
                                        <td>{{ $equipment->name }}</td>
                                        <td>{{ $equipment->model ?? 'N/A' }}</td>
                                        <td>{{ $equipment->serial_number ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($equipment->status == 'available') bg-success
                                                @elseif($equipment->status == 'in_use') bg-primary
                                                @elseif($equipment->status == 'maintenance') bg-warning
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $equipment->notes ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $equipment->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $equipment->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $equipment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('equipments.show', $equipment) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('equipments.edit', $equipment) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('equipments.destroy', $equipment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No equipment found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $equipments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection