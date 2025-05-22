@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Create New Job Option</span>
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

                    <form action="{{ route( 'job-options.store') }}" method="POST">
                        @csrf

                        <div class="d-component-container">
                            <!-- Option Name Field -->
                            <div class="form-group mb-4">
                                <label for="name">Option Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description Field -->
                            <div class="form-group mb-4">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Option Type Field -->
                            <div class="form-group mb-4">
                                <label for="option_type">Option Type</label>
                                <select class="form-control @error('option_type') is-invalid @enderror" id="option_type" name="option_type" required onchange="toggleOptionsJson()">
                                    <option value="">Select Option Type</option>
                                    <option value="text" {{ old('option_type') == 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="number" {{ old('option_type') == 'number' ? 'selected' : '' }}>Number</option>
                                    <option value="date" {{ old('option_type') == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="select" {{ old('option_type') == 'select' ? 'selected' : '' }}>Select</option>
                                    <option value="checkbox" {{ old('option_type') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                    <option value="file" {{ old('option_type') == 'file' ? 'selected' : '' }}>File</option>
                                </select>
                                @error('option_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Options JSON Field (for select type) -->
                            <div class="form-group mb-4" id="options_json_group" style="display: none;">
                                <label for="options_json">Options (JSON format)</label>
                                <textarea class="form-control @error('options_json') is-invalid @enderror" id="options_json" name="options_json" rows="4" placeholder='{"options": ["Option 1", "Option 2", "Option 3"]}'>{{ old('options_json') }}</textarea>
                                <small class="form-text text-muted">For select type, provide options in JSON format. Example: {"options": ["Option 1", "Option 2"]}</small>
                                @error('options_json')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Required Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Required</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="required" {{ old('required') ? 'checked' : '' }} />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
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
                                <button type="submit" class="btn btn-primary">Create Job Option</button>
                                <a href="{{ route( 'job-options.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOptionsJson() {
    const optionType = document.getElementById('option_type').value;
    const optionsJsonGroup = document.getElementById('options_json_group');

    if (optionType === 'select') {
        optionsJsonGroup.style.display = 'block';
    } else {
        optionsJsonGroup.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleOptionsJson();
});
</script>
@endsection
