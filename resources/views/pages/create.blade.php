@extends('layouts.app')

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
                            <label for="code">Page Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g. 3.1" required>
                                {{-- <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-info-circle" data-toggle="tooltip" title="Format: CategoryID.PageNumber (e.g. 3.1 for 'Create Client' in Client category)"></i>
                                    </span>
                                </div> --}}
                            </div>
                            <small class="form-text text-muted">The code will be in the format: CategoryID.PageNumber </small>
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
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" id="active" name="active" checked />
                                    <span class="d-slider " >
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>

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

@endpush
