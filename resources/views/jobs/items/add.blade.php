@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Add Items to Job: {{ $job->id }}</span>
                        @if($job->approval_status === 'requested')
                            <span class="badge bg-warning ms-2">In Approval Process</span>
                        @endif
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
                                    <strong>Approval:</strong>
                                    @if($job->approval_status === 'requested')
                                        <span class="badge bg-warning">Pending Approval</span>
                                    @elseif($job->approval_status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-secondary">Not Requested</span>
                                    @endif
                                </div>
                            </div>
                            @if($job->description)
                                <div class="mt-2">
                                    <strong>Description:</strong> {{ $job->description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Process Warning -->
                    @if($job->approval_status === 'requested')
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Job Currently in Approval Process
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3">
                                    <strong>Important:</strong> This job has items pending approval. Any new items you add will be marked as additional items and will be reviewed along with the existing approval request.
                                </div>
                                <p class="mb-0">
                                    <strong>Approval Requested From:</strong>
                                    @if($job->request_approval_from)
                                        {{ \App\Models\User::find($job->request_approval_from)->name ?? 'Unknown' }}
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Existing Job Items -->
                    @if($jobItems->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    Current Job Items
                                    <span class="badge bg-secondary">{{ $jobItems->count() }} items</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item/Custom Item</th>
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

                                                        @if($jobItem->item_id)
                                                            <strong>{{ $jobItem->item->name }}</strong>
                                                            @if($jobItem->sku)
                                                                <br><small class="text-muted">SKU: {{ $jobItem->sku }}</small>
                                                            @endif
                                                            @endif
                                                        @if ($jobItem->custom_item_description)
                                                            <span class="text-info">{{ $jobItem->custom_item_description }}</span>
                                                            <br><small class="text-muted">(New Item)</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $jobItem->quantity }}</strong>
                                                        @if($jobItem->unit)
                                                            <small class="text-muted">{{ $jobItem->unit }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $jobItem->pivot->notes ?? '-' }}</td>
                                                    <td>{{ \App\Models\User::find($jobItem->added_by)->name ?? 'N/A' }}</td>
                                                    <td>{{ $jobItem->added_at ? \Carbon\Carbon::parse($jobItem->added_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst(str_replace('_', ' ', $jobItem->addition_stage)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($job->approval_status !== 'requested')
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-info-ciraddedcle"></i>
                                        <strong>Note:</strong> You can update quantities of existing items below, or add new items.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('jobs.items.store', $job) }}" method="POST" id="items-form">
                        @csrf

                        <!-- Issue Description -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    @if($job->approval_status === 'requested')
                                        Additional Items Justification <span class="text-danger">*</span>
                                    @else
                                        Issue Description <span class="text-danger">*</span>
                                    @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control @error('issue_description') is-invalid @enderror"
                                          name="issue_description"
                                          rows="4"
                                          placeholder="@if($job->approval_status === 'requested')Explain why additional items are needed for this job...@elseDescribe the issue or reason for adding these items...@endif"
                                          required>{{ old('issue_description') }}</textarea>
                                @error('issue_description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Quick Close Option (only if not in approval process) -->
                        @if($job->approval_status !== 'requested')
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle"></i>
                                    Minor Issue - Close Job Immediately
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="close_job" name="close_job" value="1">
                                    <label class="form-check-label" for="close_job">
                                        <strong>Mark this job as completed (minor issue that doesn't require items)</strong>
                                        <br>
                                        <small class="text-muted">
                                            Check this if the issue is minor and can be resolved without adding any items.
                                            The job will be marked as completed immediately without requiring approval.
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Items Section -->
                        <div id="items-section" class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    @if($job->approval_status === 'requested')
                                        Add Additional Items
                                        <small class="text-muted">(These will be added to the existing approval request)</small>
                                    @else
                                        Manage Job Items
                                    @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Update Existing Items (only show if items exist and not in approval) -->
                       @if($jobItems->count() > 0 && $job->approval_status !== 'requested')
<div class="mb-4">
    <h6 class="text-primary">
        <i class="fas fa-edit"></i> Update Existing Items
    </h6>
    <div class="alert alert-info">
        <small>You can modify the quantities and notes for items already added to this job.</small>
    </div>
    @foreach($jobItems as $index => $jobItem)
        @if($jobItem->item_id) {{-- Only show items from inventory, not custom items --}}
            <div class="row mb-2 align-items-center border-bottom pb-2">
                <div class="col-md-5">
                    <strong>{{ $jobItem->item->name }}</strong>
                    @if($jobItem->item->sku)
                        <br><small class="text-muted">SKU: {{ $jobItem->item->sku }}</small>
                    @endif
                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $jobItem->item_id }}">
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">Quantity</label>
                    <input type="number"
                           class="form-control form-control-sm"
                           name="items[{{ $index }}][quantity]"
                           value="{{ $jobItem->quantity }}"
                           min="0.01"
                           step="0.01">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Notes</label>
                    <input type="text"
                           class="form-control form-control-sm"
                           name="items[{{ $index }}][notes]"
                           value="{{ $jobItem->notes }}"
                           placeholder="Add notes...">
                </div>
                <div class="col-md-1 text-center">
                    <i class="fas fa-edit text-primary"></i>
                </div>
            </div>
        @endif
    @endforeach
    <hr>
</div>
@endif

                                <!-- Add New Items from Inventory -->
                                <div class="mb-4">
                                    <h6 class="text-success">
                                        <i class="fas fa-plus"></i> Add Items from Inventory
                                    </h6>
                                    <div id="existing-items-container">
                                        <div class="existing-item-row row mb-2">
                                            <div class="col-md-5">
                                                <select class="form-control" name="items[{{ $jobItems->count() }}][item_id]">
                                                    <option value="">Select Item from Inventory</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name }}
                                                            @if($item->sku) - {{ $item->sku }} @endif
                                                            @if($item->unit_price) (₹{{ number_format($item->unit_price, 2) }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="items[{{ $jobItems->count() }}][quantity]"
                                                       placeholder="Quantity" min="0.01" step="0.01">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="items[{{ $jobItems->count() }}][notes]"
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

                                <!-- Add New Items (Not in inventory) -->
                                <div class="mb-4">
                                    <h6 class="text-warning">
                                        <i class="fas fa-box"></i> Add New Items (Not in Inventory)
                                    </h6>
                                    <div class="alert alert-warning">
                                        <small>These items will be added to the inventory after approval.</small>
                                    </div>
                                    <div id="new-items-container">
                                        <div class="new-item-row row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="new_items[0][description]"
                                                       placeholder="Describe the new item...">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="new_items[0][quantity]"
                                                       placeholder="Quantity" min="0.01" step="0.01">
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-control-plaintext text-muted small">
                                                    <i class="fas fa-info-circle"></i> Will be added to inventory
                                                </span>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-warning btn-sm add-new-item">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Section -->
                        @if($job->approval_status !== 'requested')
                        <div id="approval-section" class="card mb-4">
                          
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="request_approval_from">Request approval from:</label>
                                    <select class="form-control" name="request_approval_from" id="request_approval_from">
                                        <option value="">Select approver </option>
                                        @foreach($approvalUsers as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} ({{ $user->userRole->name ?? 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Select a user to request approval for this job with the added items. If not selected, items will be saved without requesting approval.
                                    </small>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle"></i> Approval Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong>This job is already in the approval process.</strong>
                                </p>
                                <p class="mb-2">
                                    <strong>Current Approver:</strong>
                                    {{ \App\Models\User::find($job->request_approval_from)->name ?? 'Unknown' }}
                                </p>
                                <p class="mb-0 text-muted">
                                    Any items you add will be included in the existing approval request.
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i>
                                @if($job->approval_status === 'requested')
                                    Add Additional Items
                                @else
                                    Modify Job
                                @endif
                            </button>
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary btn-sm ms-2">
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
    let existingItemIndex = {{ $jobItems->count() + 1 }};
    let newItemIndex = 1;

    const isInApprovalProcess = {{ $job->approval_status === 'requested' ? 'true' : 'false' }};

    // Handle close job checkbox (only if not in approval process)
    if (!isInApprovalProcess) {
        $('#close_job').change(function() {
            if ($(this).is(':checked')) {
                $('#items-section, #approval-section').hide();
                // Clear all item inputs when closing job
                $('input[name*="items"], input[name*="new_items"], select[name*="items"]').val('');
            } else {
                $('#items-section, #approval-section').show();
            }
        });
    }

    // Add existing item row
    $(document).on('click', '.add-existing-item', function() {
        const itemsHtml = '{!! $items->map(function($item) {
            return sprintf('<option value="%s">%s%s%s</option>',
                $item->id,
                $item->name,
                $item->sku ? " - {$item->sku}" : "",
                $item->unit_price ? " (₹" . number_format($item->unit_price, 2) . ")" : ""
            );
        })->implode('') !!}';

        const newRow = `
            <div class="existing-item-row row mb-2">
                <div class="col-md-5">
                    <select class="form-control" name="items[${existingItemIndex}][item_id]">
                        <option value="">Select Item from Inventory</option>
                        ${itemsHtml}
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
                           placeholder="Describe the new item...">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="new_items[${newItemIndex}][quantity]"
                           placeholder="Quantity" min="0.01" step="0.01">
                </div>
                <div class="col-md-3">
                    <span class="form-control-plaintext text-muted small">
                        <i class="fas fa-info-circle"></i> Will be added to inventory
                    </span>
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
        // If closing job, no need to validate items
        if ($('#close_job').is(':checked')) {
            return true;
        }

        let hasItems = false;
        let hasUpdatedExistingItems = false;

        // Check if any existing items have been updated (for non-approval process jobs)
        if (!isInApprovalProcess) {
            $('input[name*="items"][name*="quantity"]').each(function() {
                if ($(this).val() && $(this).val() > 0) {
                    hasUpdatedExistingItems = true;
                    hasItems = true;
                }
            });
        }

        // Check new items from inventory
        $('select[name*="items"][name*="item_id"]').each(function() {
            const itemId = $(this).val();
            const quantity = $(this).closest('.row').find('input[name*="quantity"]').val();
            if (itemId && quantity && quantity > 0) {
                hasItems = true;
                return false;
            }
        });

        // Check new items (not in inventory)
        if (!hasItems) {
            $('input[name*="new_items"][name*="description"]').each(function() {
                const description = $(this).val();
                const quantity = $(this).closest('.row').find('input[name*="quantity"]').val();
                if (description && quantity && quantity > 0) {
                    hasItems = true;
                    return false;
                }
            });
        }

        if (!hasItems) {
            alert('Please add at least one item with quantity, update existing items, or check "Close Job" if this is a minor issue.');
            e.preventDefault();
            return false;
        }

        // Confirmation for approval process
        if (isInApprovalProcess) {
            if (!confirm('This will add additional items to the existing approval request. Continue?')) {
                e.preventDefault();
                return false;
            }
        }

        return true;
    });
});
</script>

<style>
.bg-light-success {
    background-color: #d1f2eb !important;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}

.small {
    font-size: 0.875rem;
}

.alert {
    margin-bottom: 1rem;
}

.btn-lg {
    padding: 0.5rem 1rem;
    font-size: 1.25rem;
    border-radius: 0.3rem;
}
</style>
@endsection
