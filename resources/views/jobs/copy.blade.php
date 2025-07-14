@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:600px;">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Copy Job: {{ $job->id }}</span>
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

                    <form action="{{ route('jobs.copy.store', $job) }}" method="POST" enctype="multipart/form-data" id="job-copy-form">
                        @csrf

                        <div class="d-component-container">
                            <div class="row">
                                <!-- Job Type Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="job_type_id">Job Type</label>
                                        <select class="form-control @error('job_type_id') is-invalid @enderror" id="job_type_id" name="job_type_id" required>
                                            <option value="">Select Job Type</option>
                                            @foreach($jobTypes as $jobType)
                                                <option value="{{ $jobType->id }}" {{ old('job_type_id', $job->job_type_id) == $jobType->id ? 'selected' : '' }}>
                                                    {{ $jobType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('job_type_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Priority Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="priority">Priority</label>
                                        <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="1" {{ old('priority', $job->priority) == '1' ? 'selected' : '' }}>Critical</option>
                                            <option value="2" {{ old('priority', $job->priority) == '2' ? 'selected' : '' }}>High</option>
                                            <option value="3" {{ old('priority', $job->priority) == '3' ? 'selected' : '' }}>Medium</option>
                                            <option value="4" {{ old('priority', $job->priority) == '4' ? 'selected' : '' }}>Low</option>
                                        </select>
                                        @error('priority')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Description Field -->
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Enter job description">{{ old('description', $job->description) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- References Field -->
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label for="references">References</label>
                                        <textarea class="form-control @error('references') is-invalid @enderror" id="references" name="references" rows="2" placeholder="Enter any references">{{ old('references', $job->references) }}</textarea>
                                        @error('references')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Start Date Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $job->start_date) }}">
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Due Date Field -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $job->due_date) }}">
                                        @error('due_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Job Options Container -->
                            <div id="job-options-container" style="display: none;">
                                <div class="form-group mb-4">
                                    <h6>Job Type Specific Options</h6>
                                    <div id="job-options-fields"></div>
                                </div>
                            </div>

                            <!-- Photos Field -->
                            <div class="form-group mb-4">
                                <label for="photos">Photos</label>
                                <input type="file" class="form-control @error('photos.*') is-invalid @enderror" id="photos" name="photos[]" multiple accept="image/*">
                                <small class="form-text text-muted">Upload new photos (optional). Original job photos will be copied if no new photos are uploaded.</small>
                                @error('photos.*')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Create Copy of Job</button>
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ms-2">Cancel</a>
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
    const existingJobOptionValues = @json($jobOptionValues); // This now comes from separate table
    const existingEquipmentId = @json($job->equipment_id);
    const existingClientId = @json($job->client_id);

    $('#job_type_id').change(function() {
        const jobTypeId = $(this).val();
        const jobOptionsContainer = $('#job-options-container');
        const jobOptionsFields = $('#job-options-fields');

        jobOptionsFields.empty();
        jobOptionsContainer.hide();

        if (jobTypeId) {
            $.ajax({
                url: '/jobs/job-types/' + jobTypeId + '/options',
                type: 'GET',
                success: function(response) {
                    if (response.job_options && response.job_options.length > 0) {
                        response.job_options.forEach(function(option) {
                            const fieldName = 'job_option_' + option.id;
                            const isRequired = option.required ? 'required' : '';
                            let fieldHtml = '';
                            let selectedValue = '';

                            // Get the existing value for this option
                            if (option.id == 1) {
                                selectedValue = existingEquipmentId;
                            } else if (option.id == 2) {
                                selectedValue = existingClientId;
                            } else {
                                selectedValue = existingJobOptionValues[option.id] || '';
                            }

                            if (option.option_type === 'select') {
                                if (option.id == 1) { // Equipment
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">Equipment${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select Equipment</option>`;
                                    equipments.forEach(function(equipment) {
                                        const selected = equipment.id == selectedValue ? 'selected' : '';
                                        fieldHtml += `<option value="${equipment.id}" ${selected}>${equipment.name}</option>`;
                                    });
                                    fieldHtml += `</select></div>`;
                                } else if (option.id == 2) { // Client
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">Client${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select Client</option>`;
                                    clients.forEach(function(client) {
                                        const selected = client.id == selectedValue ? 'selected' : '';
                                        fieldHtml += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                                    });
                                    fieldHtml += `</select></div>`;
                                } else {
                                    // Other select options
                                    fieldHtml = `
                                        <div class="form-group mb-3">
                                            <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                            <select class="form-control" id="${fieldName}" name="${fieldName}" ${isRequired}>
                                                <option value="">Select ${option.name}</option>`;
                                    if (option.options_json) {
                                        try {
                                            const options = JSON.parse(option.options_json);
                                            options.forEach(function(opt) {
                                                const selected = opt.value == selectedValue ? 'selected' : '';
                                                fieldHtml += `<option value="${opt.value}" ${selected}>${opt.label}</option>`;
                                            });
                                        } catch (e) {
                                            console.error('Error parsing options JSON:', e);
                                        }
                                    }
                                    fieldHtml += `</select></div>`;
                                }
                            } else if (option.option_type === 'checkbox') {
                                const checked = selectedValue ? 'checked' : '';
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="${fieldName}" name="${fieldName}" value="1" ${checked}>
                                            <label class="form-check-label" for="${fieldName}">
                                                ${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}
                                            </label>
                                        </div>
                                    </div>`;
                            } else if (option.option_type === 'file') {
                                let currentFileHtml = '';
                                if (selectedValue) {
                                    currentFileHtml = `<small class="form-text text-muted">Current file: ${selectedValue}</small>`;
                                }
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="file" class="form-control" id="${fieldName}" name="${fieldName}">
                                        ${currentFileHtml}
                                    </div>`;
                            } else if (option.option_type === 'date') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="date" class="form-control" id="${fieldName}" name="${fieldName}" value="${selectedValue}" ${isRequired}>
                                    </div>`;
                            } else if (option.option_type === 'number') {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="number" class="form-control" id="${fieldName}" name="${fieldName}" value="${selectedValue}" ${isRequired}>
                                    </div>`;
                            } else {
                                fieldHtml = `
                                    <div class="form-group mb-3">
                                        <label for="${fieldName}">${option.name}${isRequired ? ' <span class="text-danger">*</span>' : ''}</label>
                                        <input type="text" class="form-control" id="${fieldName}" name="${fieldName}" value="${selectedValue}" ${isRequired}>
                                    </div>`;
                            }

                            jobOptionsFields.append(fieldHtml);
                        });
                        jobOptionsContainer.show();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                    alert('Error fetching job options');
                }
            });
        }
    });

    // Trigger change event if job type is already selected
    if ($('#job_type_id').val()) {
        $('#job_type_id').trigger('change');
    }
});
</script>
@endsection
