@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Job Approval: {{ $job->id }}</span>
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
                                    <span class="badge bg-warning">{{ ucfirst($job->status) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Equipment:</strong> {{ $job->equipment->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <strong>Start Date:</strong> {{ $job->start_date ? \Carbon\Carbon::parse($job->start_date)->format('Y-m-d') : 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Due Date:</strong> {{ $job->due_date ? \Carbon\Carbon::parse($job->due_date)->format('Y-m-d') : 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Approval Status:</strong>
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $job->approval_status)) }}</span>
                                </div>
                            </div>
                            @if($job->description)
                                <div class="mt-2">
                                    <strong>Description:</strong> {{ $job->description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Issue Description -->
                    @if($jobItems->count() > 0 && $jobItems->first()->pivot->issue_description)
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Issue Description
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $jobItems->first()->pivot->issue_description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Current Job Items -->
                    @if($jobItems->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Requested Items</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Notes</th>
                                                <th>Requested By</th>
                                                <th>Requested At</th>
                                                <th>Stage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jobItems as $jobItem)
                                                <tr>
                                                    <td>
                                                        @if($jobItem->name)
                                                            <strong>{{ $jobItem->name }}</strong>
                                                            @if($jobItem->sku)
                                                                <br><small class="text-muted">SKU: {{ $jobItem->sku }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-info">{{ $jobItem->pivot->custom_item_description }}</span>
                                                            <br><small class="text-muted">(New Item)</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $jobItem->pivot->quantity }}</strong>
                                                        @if($jobItem->unit)
                                                            <small class="text-muted">{{ $jobItem->unit }}</small>
                                                        @endif
                                                    </td>
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

                    <form action="{{ route('jobs.items.process-approval', $job) }}" method="POST" id="approval-form">
                        @csrf

                        <!-- Edit Items Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-edit"></i>
                                    Edit Items (Optional)
                                </h6>
                                <small class="text-muted">You can modify quantities or add additional items before approval</small>
                            </div>
                            <div class="card-body">
                                <!-- Edit Existing Items -->
                                @if($jobItems->count() > 0)
                                    <div class="mb-4">
                                        <h6>Modify Existing Items</h6>
                                        @foreach($jobItems as $index => $jobItem)
                                            @if($jobItem->pivot->item_id)
                                                <div class="row mb-2 align-items-center">
                                                    <div class="col-md-5">
                                                        <strong>{{ $jobItem->name }}</strong>
                                                        @if($jobItem->sku)
                                                            <br><small class="text-muted">SKU: {{ $jobItem->sku }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number"
                                                               class="form-control"
                                                               name="items[{{ $jobItem->pivot->item_id }}][quantity]"
                                                               value="{{ $jobItem->pivot->quantity }}"
                                                               min="0.01"
                                                               step="0.01">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text"
                                                               class="form-control"
                                                               name="items[{{ $jobItem->pivot->item_id }}][notes]"
                                                               value="{{ $jobItem->pivot->notes }}"
                                                               placeholder="Notes">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <span class="text-muted">
                                                            <i class="fas fa-edit"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <hr>

                                <!-- Add Additional Items -->
                                <div class="mb-4">
                                    <h6>Add Additional Items</h6>
                                    <div id="additional-items-container">
                                        <div class="additional-item-row row mb-2">
                                            <div class="col-md-5">
                                                <select class="form-control" name="additional_items[0][item_id]">
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
                                                <input type="number" class="form-control" name="additional_items[0][quantity]"
                                                       placeholder="Quantity" min="0.01" step="0.01">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="additional_items[0][notes]"
                                                       placeholder="Notes (optional)">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-success btn-sm add-additional-item">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add New Items (Not in inventory) -->
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
                                                <span class="form-control-plaintext text-muted">Will be added to inventory</span>
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

                        <!-- Approval Notes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Approval Notes</h6>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control @error('approval_notes') is-invalid @enderror"
                                          name="approval_notes"
                                          rows="4"
                                          placeholder="Add any notes regarding your approval or rejection...">{{ old('approval_notes') }}</textarea>
                                @error('approval_notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <form class="row">
                                    {{-- form submission for approving or rejecting --}}
                                    <form method="POST" action="{{ route('jobs.items.process-approval', $job) }}" id="approval-form">
                                        @csrf
                                        <input type="hidden" name="action" value="">
                                        <button type="submit" class="btn btn-success btn-lg me-2" value="approve">
                                            <i class="fas fa-check"></i> Approve Job
                                        </button>
                                        <button type="submit" class="btn btn-danger btn-lg" value="reject">
                                            <i class="fas fa-times"></i> Reject Job
                                        </button>
                                        <button type="submit" class="btn btn-secondary btn-lg" value="back">
                                            <i class="fas fa-arrow-left"></i> Back to Jobs
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="text-center">
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Jobs
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
    let additionalItemIndex = 1;
    let newItemIndex = 1;

    // Add additional item row
    $(document).on('click', '.add-additional-item', function() {
        const newRow = `
            <div class="additional-item-row row mb-2">
                <div class="col-md-5">
                    <select class="form-control" name="additional_items[${additionalItemIndex}][item_id]">
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
                    <input type="number" class="form-control" name="additional_items[${additionalItemIndex}][quantity]"
                           placeholder="Quantity" min="0.01" step="0.01">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="additional_items[${additionalItemIndex}][notes]"
                           placeholder="Notes (optional)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#additional-items-container').append(newRow);
        additionalItemIndex++;
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
                    <span class="form-control-plaintext text-muted">Will be added to inventory</span>
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

    // Confirmation for approval/rejection
    $('#approval-form').submit(function(e) {
        const action = $('button[type="submit"]:focus').val() || $('input[name="action"]:checked').val();

        if (action === 'approve') {
            if (!confirm('Are you sure you want to approve this job? You will be redirected to add tasks.')) {
                e.preventDefault();
                return false;
            }
        } else if (action === 'reject') {
            if (!confirm('Are you sure you want to reject this job? This action cannot be undone easily.')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Store which button was clicked
    $('button[type="submit"]').click(function() {
        $('input[name="action"]').remove();
        $(this).after('<input type="hidden" name="action" value="' + $(this).val() + '">');
    });
});
</script>

<style>
.card-header h6 {
    margin-bottom: 0;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.8rem;
}
</style>
@endsection
