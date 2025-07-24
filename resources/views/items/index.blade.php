@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Items</span>
                            </div>
                            @if(App\Helpers\UserRoleHelper::hasPermission('10.2'))
                            <a href="{{ route('items.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Item
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
                                        <th>SKU</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                @if($item->sku)
                                                    <code>{{ $item->sku }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->unit ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $item->quantity > 0 ? 'bg-success' : 'bg-warning' }}">
                                                    {{ number_format($item->quantity) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $item->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $item->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                              
@if(App\Helpers\UserRoleHelper::hasPermission('10.3'))
                                                <a href="{{ route('items.edit', $item->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                @if(App\Helpers\UserRoleHelper::hasPermission('10.4'))

                                                <form action="{{ route('items.destroy', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($items->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center">No items found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $items->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection