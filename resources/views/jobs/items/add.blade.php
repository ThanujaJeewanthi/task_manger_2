@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Add Items to Job: {{ $job->id }}</span>
                    </div>
                    <div class="d-component-subtitle">
                        <span>{{ $job->jobType->name ?? 'N/A' }} - {{ $job->client->name ?? 'No Client' }}</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Job Details Summary -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Job Details</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Job Id:</strong> {{ $job->id }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Priority:</strong>
                                    @switch($job->priority)
                                        @case(1) <span class="badge bg-danger">High</span> @break
                                        @case(2) <span class="badge bg-warning">Medium</span> @break
                                        @case(3) <span class="badge bg-info">Low</span> @break
                                        @case(4) <span class="badge bg-secondary">Very Low</span> @break
                                    @endswitch
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong>
                                    <span class="badge bg-primary">{{ ucfirst($job->status) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Equipment:</strong> {{ $job->equipment->name ?? 'N/A' }}
                                </div>
                            </div>
                            @if($job->description)
                                <div class="mt-2">
                                    <strong>Description:</strong> {{ $job->description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Existing Job Items -->
                    @if($jobItems->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Current Job Items</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Notes</th>
                                                <th>Added By</th>
                                                <th>Added At</th>
                                                <th>Stage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jobItems as $jobItem)
                                                <tr>
                                                    <td>
                                                        {{ $jobItem->name ?? $jobItem->pivot->custom_item_description }}
                                                    </td>
                                                    <td>{{ $jobItem->pivot->quantity }}</td>
                                                    <td>{{ $jobItem->pivot->notes ?? '-' }}</td>
                                                    <td>{{ $jobItem->pivot->added_by ? \App\Models\User::find($jobItem->pivot->added_by)->name : 'N/A' }}</td>
                                                    <td>{{ $jobItem->pivot->added_at ? \Carbon\Carbon::parse($jobItem->pivot->added_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst(str_replace('_', ' ', $jobItem->pivot->addition_stage)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('jobs.items.store', $job) }}" method="POST" id="items-form">
                        @csrf

                        <!-- Issue Description -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Issue Description <span class="text-danger">*</span></h6>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control @error('issue_description') is-invalid @enderror"
                                          name="issue_description"
                                          rows="4"
                                          placeholder="Describe the issue or reason for adding these items..."
                                          required>{{ old('issue_description') }}</textarea>
                                @error('issue_description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Quick Close Option -->
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-light-success">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Minor Issue - Close Job
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="close_job" name="close_job" value="1">
                                    <label class="form-check-label" for="close_job">
                                        <strong>Mark this job as completed (minor issue)</strong>
                                        <br>
                                        <small class="text-muted">
                                            Check this if the issue is minor and doesn't require material/items.
                                            The job will be marked as completed immediately.
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div id="items-section" class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Add Items to Job</h6>
                            </div>
                            <div class="card-body">
                                <!-- Existing Items -->
                                <div class="mb-4">
                                    <h6>Select from Existing Items</h6>
                                    <div id="existing-items-container">
                                        <div class="existing-item-row row mb-2">
                                            <div class="col-md-5">
                                                <select class="form-control" name="items[0][item_id]">
                                                    <option value="">Select Item</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name }}
                                                            @if($item->sku) - {{ $item->sku }} @endif
                                                            @if($item->unit_price) ({{ number_format($item->unit_price, 2) }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="items[0][quantity]"
                                                       placeholder="Quantity" min="0.01" step="0.01">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="items[0][notes]"
                                                       placeholder="Notes (optional)">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-success btn-sm add-existing-item">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- New Items -->
                                <div class="mb-4">
                                    <h6>Add New Items (Not in inventory)</h6>
                                    <div id="new-items-container">
                                        <div class="new-item-row row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="new_items[0][description]"
                                                       placeholder="Item description">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="new_items[0][quantity]"
                                                       placeholder="Quantity" min="0.01" step="0.01">
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-control-plaintext text-muted">Will be added to inventory later</span>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-success btn-sm add-new-item">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Section -->
                        <div id="approval-section" class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Request Approval</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="request_approval_from">Request approval from:</label>
                                    <select class="form-control" name="request_approval_from" id="request_approval_from">
                                        <option value="">Select approver (optional)</option>
                                        @foreach($approvalUsers as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->username }} ({{ $user->userRole->name ?? 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Select a user to request approval for this job with the added items.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Items & Request Approval
                            </button>
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-arrow-left"></i> Back to Job
                            </a>
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
    let existingItemIndex = 1;
    let newItemIndex = 1;

    // Handle close job checkbox
    $('#close_job').change(function() {
        if ($(this).is(':checked')) {
            $('#items-section, #approval-section').hide();
        } else {
            $('#items-section, #approval-section').show();
        }
    });

    // Add existing item row
    $(document).on('click', '.add-existing-item', function() {
        const newRow = `
            <div class="existing-item-row row mb-2">
                <div class="col-md-5">
                    <select class="form-control" name="items[${existingItemIndex}][item_id]">
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                                @if($item->sku) - {{ $item->sku }} @endif
                                @if($item->unit_price) ({{ number_format($item->unit_price, 2) }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="items[${existingItemIndex}][quantity]"
                           placeholder="Quantity" min="0.01" step="0.01">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="items[${existingItemIndex}][notes]"
                           placeholder="Notes (optional)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#existing-items-container').append(newRow);
        existingItemIndex++;
    });

    // Add new item row
    $(document).on('click', '.add-new-item', function() {
        const newRow = `
            <div class="new-item-row row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="new_items[${newItemIndex}][description]"
                           placeholder="Item description">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="new_items[${newItemIndex}][quantity]"
                           placeholder="Quantity" min="0.01" step="0.01">
                </div>
                <div class="col-md-3">
                    <span class="form-control-plaintext text-muted">Will be added to inventory later</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#new-items-container').append(newRow);
        newItemIndex++;
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.row').remove();
    });

    // Form validation
    $('#items-form').submit(function(e) {
        if (!$('#close_job').is(':checked')) {
            let hasItems = false;

            // Check existing items
            $('select[name*="[item_id]"]').each(function() {
                const itemId = $(this).val();
                const quantity = $(this).closest('.row').find('input[name*="[quantity]"]').val();
                if (itemId && quantity && quantity > 0) {
                    hasItems = true;
                    return false;
                }
            });

            // Check new items
            if (!hasItems) {
                $('input[name*="new_items"][name*="[description]"]').each(function() {
                    const description = $(this).val();
                    const quantity = $(this).closest('.row').find('input[name*="[quantity]"]').val();
                    if (description && quantity && quantity > 0) {
                        hasItems = true;
                        return false;
                    }
                });
            }

            if (!hasItems) {
                alert('Please add at least one item with quantity, or check "Close Job" if this is a minor issue.');
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>

<style>
.bg-light-success {
    background-color: #d1f2eb !important;
}
</style>
@endsection
