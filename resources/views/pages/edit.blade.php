@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Page</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Page Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $page->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Page Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $page->code) }}" required>
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
                                    <option value="{{ $id }}" {{ old('page_category_id', $page->page_category_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('page_category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" {{ $page->active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="active">Active</label>
                            </div>
                        </div> --}}
                        <div class="form-group">
                        <div class="d-com-flex justify-content-start mb-4">
                            <label class="custom-control-label" for="active">Active</label>
                            <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                <input type="checkbox" class="custom-control-input" id="active" name="active" {{ $page->active ? 'checked' : '' }}>
                               <span class="d-slider " >
                                    <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                    <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                </span>
                            </label>
                        </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Page</button>
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
    });
</script>
@endpush
