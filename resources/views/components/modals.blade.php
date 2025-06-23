{{-- resources/views/components/modals.blade.php --}}

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <i id="confirmModalIcon" class="fas fa-question-circle text-warning me-3" style="font-size: 2rem;"></i>
                    <div>
                        <p id="confirmModalMessage" class="mb-0">Are you sure you want to proceed?</p>
                        <small id="confirmModalSubmessage" class="text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmModalAction" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Input Modal (for prompts) -->
<div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputModalTitle">Input Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="inputModalText" class="form-label" id="inputModalLabel">Please provide input:</label>
                    <div id="inputModalInputContainer">
                        <!-- Input field will be dynamically added here -->
                    </div>
                    <div id="inputModalError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="inputModalSubmit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i id="messageModalIcon" class="fas fa-info-circle text-info me-3" style="font-size: 2rem;"></i>
                    <div>
                        <p id="messageModalMessage" class="mb-0">Message content</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Task Extension Request Modal -->
<div class="modal fade" id="taskExtensionModal" tabindex="-1" aria-labelledby="taskExtensionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskExtensionModalLabel">Request Task Extension</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taskExtensionForm">
                <div class="modal-body">
                    <input type="hidden" id="extensionTaskId" name="task_id">

                    <div class="mb-3">
                        <label for="extensionDays" class="form-label">Extension Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="extensionDays" name="extension_days"
                               min="1" max="30" required>
                        <div class="form-text">How many additional days do you need?</div>
                    </div>

                    <div class="mb-3">
                        <label for="extensionReason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="extensionReason" name="reason" rows="3"
                                  placeholder="Explain why you need the extension..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="extensionJustification" class="form-label">Justification</label>
                        <textarea class="form-control" id="extensionJustification" name="justification" rows="2"
                                  placeholder="Additional details or justification..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-clock"></i> Request Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job Assignment Modal -->
<div class="modal fade" id="jobAssignmentModal" tabindex="-1" aria-labelledby="jobAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobAssignmentModalLabel">Assign Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="jobAssignmentForm">
                <div class="modal-body">
                    <input type="hidden" id="assignJobId" name="job_id">

                    <div class="mb-3">
                        <label for="assignedUserId" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assignedUserId" name="assigned_user_id" required>
                            <option value="">Select Technical Officer...</option>
                            @php
                                $technicalOfficers = \App\Models\User::whereHas('userRole', function($query) {
                                    $query->where('name', 'Technical Officer');
                                })->where('company_id', Auth::user()->company_id ?? null)->get();
                            @endphp
                            @foreach($technicalOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }} ({{ $officer->username }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignPriority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="assignPriority" name="priority" required>
                            <option value="1">Low</option>
                            <option value="2" selected>Normal</option>
                            <option value="3">High</option>
                            <option value="4">Critical</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignDueDate" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="assignDueDate" name="due_date"
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label for="assignmentNotes" class="form-label">Assignment Notes</label>
                        <textarea class="form-control" id="assignmentNotes" name="assignment_notes" rows="3"
                                  placeholder="Additional instructions or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Assign Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job Status Update Modal -->
<div class="modal fade" id="jobStatusModal" tabindex="-1" aria-labelledby="jobStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobStatusModalLabel">Update Job Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="jobStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusJobId" name="job_id">

                    <div class="mb-3">
                        <label for="jobStatus" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="jobStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3" id="completionNotesContainer" style="display: none;">
                        <label for="completionNotes" class="form-label">Completion Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="completionNotes" name="completion_notes" rows="3"
                                  placeholder="Describe what was completed..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="statusNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="statusNotes" name="notes" rows="2"
                                  placeholder="Additional notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job Approval Modal -->
<div class="modal fade" id="jobApprovalModal" tabindex="-1" aria-labelledby="jobApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobApprovalModalLabel">Job Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="jobApprovalForm">
                <div class="modal-body">
                    <input type="hidden" id="approvalJobId" name="job_id">
                    <input type="hidden" id="approvalAction" name="action">

                    <div id="jobDetailsContainer" class="mb-4">
                        <!-- Job details will be loaded here -->
                    </div>

                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Approval Notes</label>
                        <textarea class="form-control" id="approvalNotes" name="approval_notes" rows="3"
                                  placeholder="Add notes about your decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="setApprovalAction('reject')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <button type="button" class="btn btn-success" onclick="setApprovalAction('approve')">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Job Creation Modal -->
<div class="modal fade" id="quickJobModal" tabindex="-1" aria-labelledby="quickJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickJobModalLabel">Create Quick Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickJobForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickJobTitle" class="form-label">Job Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quickJobTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickJobType" class="form-label">Job Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="quickJobType" name="job_type_id" required>
                                    <option value="">Select Job Type...</option>
                                    @php
                                        $jobTypes = \App\Models\JobType::where('active', true)->get();
                                    @endphp
                                    @foreach($jobTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quickJobDescription" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quickJobDescription" name="description" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickJobPriority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" id="quickJobPriority" name="priority" required>
                                    <option value="1">Low</option>
                                    <option value="2" selected>Normal</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quickJobDueDate" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="quickJobDueDate" name="due_date"
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quickJobAssignTo" class="form-label">Assign To</label>
                        <select class="form-select" id="quickJobAssignTo" name="assigned_user_id">
                            <option value="">Assign later...</option>
                            @foreach($technicalOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Job
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global Modal Utilities
class ModalUtils {
    // Show confirmation modal (replaces confirm())
    static confirm(options = {}) {
        return new Promise((resolve) => {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));

            // Set modal content
            document.getElementById('confirmModalTitle').textContent = options.title || 'Confirm Action';
            document.getElementById('confirmModalMessage').textContent = options.message || 'Are you sure?';
            document.getElementById('confirmModalSubmessage').textContent = options.submessage || '';

            // Set icon and button styling
            const icon = document.getElementById('confirmModalIcon');
            const actionBtn = document.getElementById('confirmModalAction');

            if (options.type === 'danger') {
                icon.className = 'fas fa-exclamation-triangle text-danger me-3';
                actionBtn.className = 'btn btn-danger';
                actionBtn.textContent = options.confirmText || 'Delete';
            } else if (options.type === 'warning') {
                icon.className = 'fas fa-exclamation-circle text-warning me-3';
                actionBtn.className = 'btn btn-warning';
                actionBtn.textContent = options.confirmText || 'Proceed';
            } else {
                icon.className = 'fas fa-question-circle text-info me-3';
                actionBtn.className = 'btn btn-primary';
                actionBtn.textContent = options.confirmText || 'Confirm';
            }

            // Handle button clicks
            const handleConfirm = () => {
                modal.hide();
                resolve(true);
                cleanup();
            };

            const handleCancel = () => {
                modal.hide();
                resolve(false);
                cleanup();
            };

            const cleanup = () => {
                actionBtn.removeEventListener('click', handleConfirm);
                document.getElementById('confirmModal').removeEventListener('hidden.bs.modal', handleCancel);
            };

            actionBtn.addEventListener('click', handleConfirm);
            document.getElementById('confirmModal').addEventListener('hidden.bs.modal', handleCancel, { once: true });

            modal.show();
        });
    }

    // Show input modal (replaces prompt())
    static prompt(options = {}) {
        return new Promise((resolve) => {
            const modal = new bootstrap.Modal(document.getElementById('inputModal'));

            // Set modal content
            document.getElementById('inputModalTitle').textContent = options.title || 'Input Required';
            document.getElementById('inputModalLabel').textContent = options.label || 'Please provide input:';

            // Create input field
            const container = document.getElementById('inputModalInputContainer');
            container.innerHTML = '';

            let inputElement;
            if (options.type === 'textarea') {
                inputElement = document.createElement('textarea');
                inputElement.className = 'form-control';
                inputElement.rows = options.rows || 3;
            } else {
                inputElement = document.createElement('input');
                inputElement.type = options.inputType || 'text';
                inputElement.className = 'form-control';
            }

            inputElement.id = 'inputModalText';
            inputElement.placeholder = options.placeholder || '';
            inputElement.value = options.defaultValue || '';
            if (options.required) inputElement.required = true;
            if (options.minLength) inputElement.setAttribute('minlength', options.minLength);
            if (options.maxLength) inputElement.setAttribute('maxlength', options.maxLength);

            container.appendChild(inputElement);

            // Handle validation
            const errorDiv = document.getElementById('inputModalError');
            const submitBtn = document.getElementById('inputModalSubmit');

            const validateInput = () => {
                const value = inputElement.value.trim();
                errorDiv.style.display = 'none';

                if (options.required && !value) {
                    errorDiv.textContent = 'This field is required.';
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (options.minLength && value.length < options.minLength) {
                    errorDiv.textContent = `Minimum ${options.minLength} characters required.`;
                    errorDiv.style.display = 'block';
                    return false;
                }

                if (options.validator && typeof options.validator === 'function') {
                    const validationResult = options.validator(value);
                    if (validationResult !== true) {
                        errorDiv.textContent = validationResult;
                        errorDiv.style.display = 'block';
                        return false;
                    }
                }

                return true;
            };

            // Handle button clicks
            const handleSubmit = () => {
                if (validateInput()) {
                    modal.hide();
                    resolve(inputElement.value.trim());
                    cleanup();
                }
            };

            const handleCancel = () => {
                modal.hide();
                resolve(null);
                cleanup();
            };

            const cleanup = () => {
                submitBtn.removeEventListener('click', handleSubmit);
                document.getElementById('inputModal').removeEventListener('hidden.bs.modal', handleCancel);
            };

            submitBtn.addEventListener('click', handleSubmit);
            document.getElementById('inputModal').addEventListener('hidden.bs.modal', handleCancel, { once: true });

            // Handle Enter key
            inputElement.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && options.type !== 'textarea') {
                    handleSubmit();
                }
            });

            modal.show();

            // Focus input after modal is shown
            setTimeout(() => inputElement.focus(), 100);
        });
    }

    // Show message modal (replaces alert())
    static alert(message, type = 'info', title = null) {
        return new Promise((resolve) => {
            const modal = new bootstrap.Modal(document.getElementById('messageModal'));

            // Set modal content
            const titleElement = document.getElementById('messageModalTitle');
            const messageElement = document.getElementById('messageModalMessage');
            const iconElement = document.getElementById('messageModalIcon');

            titleElement.textContent = title || this.getTitleForType(type);
            messageElement.textContent = message;

            // Set icon based on type
            const iconConfig = this.getIconConfig(type);
            iconElement.className = iconConfig.class;

            // Handle modal close
            const handleClose = () => {
                modal.hide();
                resolve();
            };

            document.getElementById('messageModal').addEventListener('hidden.bs.modal', handleClose, { once: true });

            modal.show();
        });
    }

    static getTitleForType(type) {
        const titles = {
            'success': 'Success',
            'error': 'Error',
            'warning': 'Warning',
            'info': 'Information'
        };
        return titles[type] || 'Message';
    }

    static getIconConfig(type) {
        const configs = {
            'success': { class: 'fas fa-check-circle text-success me-3' },
            'error': { class: 'fas fa-times-circle text-danger me-3' },
            'warning': { class: 'fas fa-exclamation-triangle text-warning me-3' },
            'info': { class: 'fas fa-info-circle text-info me-3' }
        };
        return configs[type] || configs.info;
    }
}

// Global functions to replace alert, confirm, prompt
window.showAlert = (message, type = 'info', title = null) => ModalUtils.alert(message, type, title);
window.showConfirm = (options) => ModalUtils.confirm(options);
window.showPrompt = (options) => ModalUtils.prompt(options);

// Specific modal functions
window.showTaskExtensionModal = function(taskId) {
    document.getElementById('extensionTaskId').value = taskId;
    const modal = new bootstrap.Modal(document.getElementById('taskExtensionModal'));
    modal.show();
};

window.showJobAssignmentModal = function(jobId) {
    document.getElementById('assignJobId').value = jobId;
    const modal = new bootstrap.Modal(document.getElementById('jobAssignmentModal'));
    modal.show();
};

window.showJobStatusModal = function(jobId, currentStatus = null) {
    document.getElementById('statusJobId').value = jobId;
    if (currentStatus) {
        document.getElementById('jobStatus').value = currentStatus;
    }
    toggleCompletionNotes();
    const modal = new bootstrap.Modal(document.getElementById('jobStatusModal'));
    modal.show();
};

window.showJobApprovalModal = function(jobId) {
    document.getElementById('approvalJobId').value = jobId;
    loadJobDetails(jobId);
    const modal = new bootstrap.Modal(document.getElementById('jobApprovalModal'));
    modal.show();
};

window.showQuickJobModal = function() {
    document.getElementById('quickJobForm').reset();
    const modal = new bootstrap.Modal(document.getElementById('quickJobModal'));
    modal.show();
};

// Helper functions
function toggleCompletionNotes() {
    const status = document.getElementById('jobStatus').value;
    const container = document.getElementById('completionNotesContainer');
    const notesField = document.getElementById('completionNotes');

    if (status === 'completed') {
        container.style.display = 'block';
        notesField.required = true;
    } else {
        container.style.display = 'none';
        notesField.required = false;
    }
}

function setApprovalAction(action) {
    document.getElementById('approvalAction').value = action;
    document.getElementById('jobApprovalForm').dispatchEvent(new Event('submit'));
}

function loadJobDetails(jobId) {
    fetch(`/api/jobs/${jobId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('jobDetailsContainer');
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">${data.job.title}</h6>
                            <p class="card-text">${data.job.description}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Job Type: ${data.job.job_type}</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Priority: ${data.job.priority_text}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading job details:', error);
        });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Job status change handler
    const jobStatusSelect = document.getElementById('jobStatus');
    if (jobStatusSelect) {
        jobStatusSelect.addEventListener('change', toggleCompletionNotes);
    }

    // Handle all modal form submissions
    handleModalFormSubmissions();
});

function handleModalFormSubmissions() {
    // Task extension form
    const taskExtensionForm = document.getElementById('taskExtensionForm');
    if (taskExtensionForm) {
        taskExtensionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const taskId = formData.get('task_id');

            try {
                const response = await fetch(`/tasks/${taskId}/request-extension`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('taskExtensionModal')).hide();
                    showToast('Extension request submitted successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to submit extension request', 'error');
                }
            } catch (error) {
                showToast('An error occurred while submitting the request', 'error');
            }
        });
    }

    // Job assignment form
    const jobAssignmentForm = document.getElementById('jobAssignmentForm');
    if (jobAssignmentForm) {
        jobAssignmentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const jobId = formData.get('job_id');

            try {
                const response = await fetch(`/supervisor/jobs/${jobId}/assign`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('jobAssignmentModal')).hide();
                    showToast('Job assigned successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to assign job', 'error');
                }
            } catch (error) {
                showToast('An error occurred while assigning the job', 'error');
            }
        });
    }

    // Job status form
    const jobStatusForm = document.getElementById('jobStatusForm');
    if (jobStatusForm) {
        jobStatusForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const jobId = formData.get('job_id');

            try {
                const response = await fetch(`/jobs/${jobId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('jobStatusModal')).hide();
                    showToast('Job status updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to update job status', 'error');
                }
            } catch (error) {
                showToast('An error occurred while updating job status', 'error');
            }
        });
    }

    // Job approval form
    const jobApprovalForm = document.getElementById('jobApprovalForm');
    if (jobApprovalForm) {
        jobApprovalForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const jobId = formData.get('job_id');

            try {
                const response = await fetch(`/engineer/jobs/${jobId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('jobApprovalModal')).hide();
                    showToast('Job approval processed successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to process job approval', 'error');
                }
            } catch (error) {
                showToast('An error occurred while processing approval', 'error');
            }
        });
    }

    // Quick job form
    const quickJobForm = document.getElementById('quickJobForm');
    if (quickJobForm) {
        quickJobForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('/jobs', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('quickJobModal')).hide();
                    showToast('Job created successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to create job', 'error');
                }
            } catch (error) {
                showToast('An error occurred while creating the job', 'error');
            }
        });
    }
}
</script>
