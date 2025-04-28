@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Permissions for Role: {{ $role->name }}</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Page</th>
                                    <th>Code</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pageCategories as $category)
                                    @foreach($category->pages as $page)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $page->name }}</td>
                                            <td>{{ $page->code }}</td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="permission_{{ $page->id }}"
                                                        name="permissions[{{ $page->code }}]"
                                                        value="1"
                                                        {{ isset($permissions[$page->code]) && $permissions[$page->code] ? 'checked' : '' }}
                                                    >
                                                    <label class="custom-control-label" for="permission_{{ $page->id }}">
                                                        {{ isset($permissions[$page->code]) && $permissions[$page->code] ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle switch label text based on checkbox state
    $('.custom-control-input').on('change', function() {
        const label = $(this).next('label');
        if ($(this).is(':checked')) {
            label.text('Enabled');
        } else {
            label.text('Disabled');
        }
    });
</script>
@endpush
