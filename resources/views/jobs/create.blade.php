@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Create New Job</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mt-3">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route( 'jobs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="d-component-container">
                            <div class="row">
                                <!-- Job Number Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="job_number">Job Number</label>
                                        <input type="text" class="form-control @error('job_number') is-invalid @enderror" id="job_number" name="job_number" value="{{ old('job_number') }}" required>
                                        @error('job_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Job Type Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="job_type_id">Job Type</label>
                                        <select class="form-control @error('job_type_id') is-invalid @enderror" id="job_type_id" name="job_type_id" required>
                                            <option value="">Select Job Type</option>
                                            @foreach($jobTypes as $jobType)
                                                <option value="{{ $jobType->id }}" {{ old('job_type_id') == $jobType->id ? 'selected' : '' }}>
                                                    {{ $jobType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('job_type_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Client Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="client_id">Client</label>
                                        <select class="form-control @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                                            <option value="">Select Client (Optional)</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Equipment Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="equipment_id">Equipment</label>
                                        <select class="form-control @error('equipment_id') is-invalid @enderror" id="equipment_id" name="equipment_id">
                                            <option value="">Select Equipment (Optional)</option>
                                            @foreach($equipments as $equipment)
                                                <option value="{{ $equipment->id }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                                    {{ $equipment->name }} - {{ $equipment->model }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('equipment_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="form-group mb-4">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- References Field -->
                            <div class="form-group mb-4">
                                <label for="references">References</label>
                                <textarea class="form-control @error('references') is-invalid @enderror" id="references" name="references" rows="3">{{ old('references') }}</textarea>
                                @error('references')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Priority Field -->
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <label for="priority">Priority</label>
                                        <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>High</option>
                                            <option value="2" {{ old('priority', '2') == '2' ? 'selected' : '' }}>Medium</option>
                                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Low</option>
                                            <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>Very Low</option>
                                        </select>
                                        @error('priority')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Start Date Field -->
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Due Date Field -->
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                                        @error('due_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Photos Field -->
                            <div class="form-group mb-4">
                                <label for="photos">Job Photos</label>
                                <input type="file" class="form-control @error('photos') is-invalid @enderror" id="photos" name="photos[]" multiple accept="image/*">
                                <small class="form-text text-muted">You can select multiple images.</small>
                                @error('photos')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Active Status Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="is_active" checked />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Job</button>
                                <a href="{{ route( 'jobs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
