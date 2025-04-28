@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Create New Page</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.pages.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Page Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Page Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g. 3.1" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Format: CategoryID.PageNumber (e.g. 3.1 for 'Create Client' in Client category)"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Use format: CategoryID.PageNumber (e.g. 3.1 for 'Create Client' in Client category)</small>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="page_category_id">Category</label>
                            <select class="form-control @error('page_category_id') is-invalid @enderror" id="page_category_id" name="page_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('page_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('page_category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                                <label class="custom-control-label" for="active">Active</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Page</button>
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
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
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // Auto-generate code based on category selection
        $('#page_category_id').on('change', function() {
            if (!$('#code').val()) {
                const categoryId = $(this).val();
                if (categoryId) {
                    $('#code').val(categoryId + '.');
                }
            }
        });
    });
</script>
@endpush
