@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Add New Equipment</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('equipments.store') }}" method="POST">
                        @csrf
                        <div class="d-component-container">

                            <div class="form-group mb-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}">
                                @error('model')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}">
                                @error('serial_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="in_use" {{ old('status') == 'in_use' ? 'selected' : '' }}>In Use</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="active" checked />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Equipment</button>
                                <a href="{{ route('equipments.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection