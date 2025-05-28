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

                    <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data" id="job-create-form">
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

                            <!-- Job Options Container -->
                            <div id="job-options-container" class="form-group mb-4" style="display: none;">
                                <label>Job Options</label>
                                <div id="job-options-fields">

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
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Job</button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const clients = @json($clients);
    const equipments = @json($equipments);

    console.log('Clients:', clients);
    console.log('Equipments:', equipments);

    $('#job_type_id').change(function() {
        const jobTypeId = $(this).val();
        const jobOptionsContainer = $('#job-options-container');
        const jobOptionsFields = $('#job-options-fields');

        console.log('Selected Job Type ID:', jobTypeId);

        jobOptionsFields.empty();
        jobOptionsContainer.hide();

        if (jobTypeId) {
            $.ajax({
                url: '/jobs/job-types/' + jobTypeId + '/options',
                type: 'GET',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.job_options && response.job_options.length > 0) {
                        console.log('Processing job options:', response.job_options);
                        response.job_options.forEach(function(option) {
                            console.log('Processing option:', option);
                            const fieldName = 'job_option_' + option.id;
                            const isRequired = option.required ? 'required' : '';
                            let fieldHtml = '';

                            if (option.option_type === 'select') {
                                if (option.id == 1) { // Equipment
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">Equipment${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select Equipment</option>
                                                ${equipments.map(e => `<option value="${e.id}">${e.name}</option>`).join('')}
                                            </select>
                                        </div>`;
                                } else if (option.id == 2) { // Client
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select Client</option>
                                                ${clients.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                                            </select>
                                        </div>`;
                                } else {
                                    // Handle other select options with options_json
                                    let options = '';
                                    if (option.options_json) {
                                        try {
                                            const optionsList = JSON.parse(option.options_json);
                                            options = optionsList.map(opt => `<option value="${opt}">${opt}</option>`).join('');
                                        } catch (e) {
                                            console.error('Error parsing options_json:', e);
                                        }
                                    }
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select ${option.name}</option>
                                                ${options}
                                            </select>
                                        </div>`;
                                }
                            } else if (option.option_type === 'textarea') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <textarea class="form-control" id="${fieldName}" name="${fieldName}" rows="3" ${isRequired}></textarea>
                                    </div>`;
                            } else if (option.option_type === 'checkbox') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="${fieldName}" name="${fieldName}" value="1">
                                            <label class="form-check-label" for="${fieldName}">
                                                ${option.name}
                                            </label>
                                        </div>
                                    </div>`;
                            } else if (option.option_type === 'file') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="file" class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                    </div>`;
                            } else if (option.option_type === 'date') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="date" class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                    </div>`;
                            } else if (option.option_type === 'number') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="number" class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                    </div>`;
                            } else {
                                // Default to text input
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="text" class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                    </div>`;
                            }

                            console.log('Generated HTML:', fieldHtml);
                            jobOptionsFields.append(fieldHtml);
                        });
                        console.log('Showing job options container');
                        jobOptionsContainer.show();
                        console.log('Container visibility after show:', jobOptionsContainer.is(':visible'));
                    } else {
                        console.log('No job options found in response');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                    alert('Error fetching job options');
                }
            });
        }
    });

    if ($('#job_type_id').val()) {
        console.log('Triggering change event on pre-selected job type');
        $('#job_type_id').trigger('change');
    }
});
</script>
@endsection
