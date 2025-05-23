@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-component-title">
                            <span>Create New Job Type</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('job-types.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="color">Color</label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                    id="color" name="color" value="{{ old('color', '#3c8dbc') }}">
                                @error('color')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Job Options</label>
                                <div class="row">
                                    @foreach($jobOptions as $option)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="job_options[]" value="{{ $option->id }}" 
                                                    id="option_{{ $option->id }}">
                                                <label class="form-check-label" for="option_{{ $option->id }}">
                                                    {{ $option->name }} ({{ $option->option_type }})
                                                </label>
                                                <input type="number" class="form-control form-control-sm mt-1" 
                                                    name="sort_orders[]" value="0" min="0" 
                                                    placeholder="Sort order">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="d-com-flex justify-content-start mb-4">
                                    <label class="d-label-text me-2">Active</label>
                                    <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                        <input type="checkbox" class="form-check-input d-section-toggle" 
                                            id="is_active" name="is_active" checked />
                                        <span class="d-slider">
                                            <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                            <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">Create Job Type</button>
                                <a href="{{ route('job-types.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection