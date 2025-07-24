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
                 @if($jobItems->count() > 0 && $jobItems->first()->issue_description)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">
                <i class="fas fa-exclamation-triangle"></i>
                Issue Description
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $jobItems->first()->issue_description }}</p>
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
                @if($jobItem->item_id)
                    <strong>{{ $jobItem->item->name }}</strong>
                    @if($jobItem->item->sku)
                        <br><small class="text-muted">SKU: {{ $jobItem->item->sku }}</small>
                    @endif
                @else
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
            <td>{{ $jobItem->notes ?? '-' }}</td>
            <td>{{ $jobItem->added_by ? \App\Models\User::find($jobItem->added_by)->name : 'N/A' }}</td>
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
            <div class="row mb-2 align-items-center">
                <div class="col-md-5">
                       @if($jobItem->item_id)
                    <strong>{{ $jobItem->item->name }}</strong>
                    @if($jobItem->item->sku)
                        <br><small class="text-muted">SKU: {{ $jobItem->item->sku }}</small>
                    @endif
                @else
                    <span class="text-info">{{ $jobItem->custom_item_description }}</span>
                    <br><small class="text-muted">(New Item)</small>
                @endif
                </div>
                <div class="col-md-2">
                    <input type="number"
                           class="form-control"
                           name="items[{{ $jobItem->id }}][quantity]"
                           value="{{ $jobItem->quantity }}"
                           min="1"
                           step="1">
                </div>
                <div class="col-md-4">
                    <input type="text"
                           class="form-control"
                           name="items[{{ $jobItem->id }}][notes]"
                           value="{{ $jobItem->notes }}"
                           placeholder="Notes">
                </div>
                <div class="col-md-1">
                    <span class="text-muted">
                        <i class="fas fa-edit"></i>
                    </span>
                </div>
            </div>
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
                                                       placeholder="Quantity" min="1" step="1">
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
                                                       placeholder="Quantity" min="1" step="1">
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
        <div class="row justify-content-center">
            <div class="col-auto">
                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success me-2">
                    <i class="fas fa-check"></i> Approve Job
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger me-2">
                    <i class="fas fa-times"></i> Reject Job
                </button>
                @if(App\Helpers\UserRoleHelper::hasPermission('11.9'))
                <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Jobs
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

                      
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const swalDefaults = {
    customClass: {
        popup: 'swal2-consistent-ui',
        confirmButton: 'btn btn-success btn-action-xs',
        cancelButton: 'btn btn-secondary btn-action-xs',
        denyButton: 'btn btn-danger btn-action-xs',
        input: 'form-control',
        title: '',
        htmlContainer: '',
    },
    buttonsStyling: false,
    background: '#fff',
    width: 420,
    showClass: { popup: 'swal2-show' },
    hideClass: { popup: 'swal2-hide' },
    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
};

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
                           placeholder="Quantity" min="1" step="1">
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
                           placeholder="Quantity" min="1" step="1">
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

    // Store which button was clicked
    $('button[type="submit"]').click(function() {
        $('input[name="action"]').remove();
        $(this).after('<input type="hidden" name="action" value="' + $(this).val() + '">');
    });

    // SweetAlert2 confirmation for approval/rejection
    $('#approval-form').on('submit', function(e) {
        const $form = $(this);
        const action = $('input[name="action"]').val();

        if (action === 'approve') {
            e.preventDefault();
            Swal.fire({
                ...swalDefaults,
                icon: 'question',
                title: '<span style="font-size:1.05rem;font-weight:600;">Approve Job?</span>',
                html: `<div style="font-size:0.92rem;">Are you sure you want to approve this job? You will be redirected to add tasks.<br><br>
                    <label for="swal-approve-notes" style="font-size:0.85rem;font-weight:500;">Approval Notes (optional):</label>
                    <textarea id="swal-approve-notes" class="form-control mt-1" style="font-size:0.88rem;" rows="2" placeholder="Add notes...">${$('[name="approval_notes"]').val() || ''}</textarea>
                </div>`,
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    return document.getElementById('swal-approve-notes').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('[name="approval_notes"]').val(result.value || '');
                    $form.off('submit').submit();
                }
            });
            return false;
        } else if (action === 'reject') {
            e.preventDefault();
            Swal.fire({
                ...swalDefaults,
                icon: 'warning',
                title: '<span style="font-size:1.05rem;font-weight:600;">Reject Job?</span>',
                html: `<div style="font-size:0.92rem;">Are you sure you want to reject this job? This action cannot be undone easily.<br><br>
                    <label for="swal-reject-notes" style="font-size:0.85rem;font-weight:500;">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea id="swal-reject-notes" class="form-control mt-1" style="font-size:0.88rem;" rows="2" placeholder="Please provide a detailed reason (min 10 characters)">${$('[name="approval_notes"]').val() || ''}</textarea>
                </div>`,
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const reason = document.getElementById('swal-reject-notes').value;
                    if (!reason || reason.trim().length < 10) {
                        Swal.showValidationMessage('Please provide a more detailed reason (at least 10 characters).');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('[name="approval_notes"]').val(result.value);
                    $form.off('submit').submit();
                }
            });
            return false;
        }
    });

    // Fade out alerts after a few seconds
    setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 5000);
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
