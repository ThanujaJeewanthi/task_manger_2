/**
 * Modern Modal System with Enhanced Error Handling
 * This replaces all existing modal implementations across the task management system
 */

class ModernModal {
    constructor(options = {}) {
        this.options = {
            title: options.title || 'Confirm Action',
            message: options.message || 'Are you sure you want to proceed?',
            confirmText: options.confirmText || 'Confirm',
            cancelText: options.cancelText || 'Cancel',
            confirmClass: options.confirmClass || 'btn-primary',
            size: options.size || 'modal-dialog', // modal-sm, modal-lg, modal-xl
            backdrop: options.backdrop !== false,
            keyboard: options.keyboard !== false,
            showCloseButton: options.showCloseButton !== false,
            autoClose: options.autoClose || 3000, // Auto close success messages
            ...options
        };

        this.modalElement = null;
        this.bootstrapModal = null;
        this.isLoading = false;
        this.onConfirm = options.onConfirm || (() => {});
        this.onCancel = options.onCancel || (() => {});
    }

    /**
     * Create and show a confirmation modal
     */
    static confirm(options) {
        const modal = new ModernModal(options);
        return modal.show();
    }

    /**
     * Create and show a form modal
     */
    static form(options) {
        const modal = new ModernModal({
            ...options,
            type: 'form'
        });
        return modal.show();
    }

    /**
     * Show success message
     */
    static success(message, autoClose = true) {
        const modal = new ModernModal({
            title: 'Success',
            message: message,
            type: 'success',
            confirmText: 'OK',
            showCancel: false,
            autoClose: autoClose ? 3000 : false
        });
        return modal.show();
    }

    /**
     * Show error message
     */
    static error(message) {
        const modal = new ModernModal({
            title: 'Error',
            message: message,
            type: 'error',
            confirmText: 'OK',
            showCancel: false,
            confirmClass: 'btn-danger'
        });
        return modal.show();
    }

    /**
     * Create modal HTML structure
     */
    createModalHTML() {
        const modalId = 'modernModal_' + Date.now();
        const { type, title, message, confirmText, cancelText, confirmClass, size, showCancel = true, showCloseButton } = this.options;

        // Icon based on type
        let icon = '';
        let alertClass = '';
        switch (type) {
            case 'success':
                icon = '<i class="fas fa-check-circle text-success me-2"></i>';
                alertClass = 'alert-success';
                break;
            case 'error':
                icon = '<i class="fas fa-exclamation-triangle text-danger me-2"></i>';
                alertClass = 'alert-danger';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-circle text-warning me-2"></i>';
                alertClass = 'alert-warning';
                break;
            default:
                icon = '<i class="fas fa-question-circle text-primary me-2"></i>';
                alertClass = 'alert-info';
        }

        const formContent = type === 'form' && this.options.formFields ? this.createFormFields() : '';

        return `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true" data-bs-backdrop="${this.options.backdrop ? 'true' : 'false'}" data-bs-keyboard="${this.options.keyboard}">
                <div class="${size}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${icon}${title}</h5>
                            ${showCloseButton ? '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' : ''}
                        </div>
                        <div class="modal-body">
                            <!-- Loading State -->
                            <div class="loading-state d-none">
                                <div class="d-flex align-items-center justify-content-center py-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span>Processing...</span>
                                </div>
                            </div>

                            <!-- Error Container -->
                            <div class="error-container d-none">
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span class="error-message"></span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            </div>

                            <!-- Main Content -->
                            <div class="main-content">
                                ${type === 'form' ? '' : `<div class="alert ${alertClass} border-0">${message}</div>`}
                                ${formContent}
                            </div>
                        </div>
                        <div class="modal-footer">
                            ${showCancel ? `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelText}</button>` : ''}
                            <button type="button" class="btn ${confirmClass} confirm-btn">
                                <span class="btn-text">${confirmText}</span>
                                <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Create form fields for form type modals
     */
    createFormFields() {
        if (!this.options.formFields) return '';

        return this.options.formFields.map(field => {
            const { type, name, label, placeholder, required, options, value } = field;

            switch (type) {
                case 'select':
                    const optionsHTML = options.map(opt =>
                        `<option value="${opt.value}" ${opt.value === value ? 'selected' : ''}>${opt.text}</option>`
                    ).join('');
                    return `
                        <div class="mb-3">
                            <label for="${name}" class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
                            <select class="form-control" name="${name}" id="${name}" ${required ? 'required' : ''}>
                                ${optionsHTML}
                            </select>
                        </div>
                    `;
                case 'textarea':
                    return `
                        <div class="mb-3">
                            <label for="${name}" class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
                            <textarea class="form-control" name="${name}" id="${name}" rows="3"
                                placeholder="${placeholder || ''}" ${required ? 'required' : ''}>${value || ''}</textarea>
                        </div>
                    `;
                case 'checkbox':
                    return `
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="${name}" id="${name}" ${value ? 'checked' : ''}>
                                <label class="form-check-label" for="${name}">${label}</label>
                            </div>
                        </div>
                    `;
                case 'date':
                    return `
                        <div class="mb-3">
                            <label for="${name}" class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
                            <input type="date" class="form-control" name="${name}" id="${name}"
                                value="${value || ''}" ${required ? 'required' : ''}>
                        </div>
                    `;
                default:
                    return `
                        <div class="mb-3">
                            <label for="${name}" class="form-label">${label}${required ? ' <span class="text-danger">*</span>' : ''}</label>
                            <input type="${type}" class="form-control" name="${name}" id="${name}"
                                placeholder="${placeholder || ''}" value="${value || ''}" ${required ? 'required' : ''}>
                        </div>
                    `;
            }
        }).join('');
    }

    /**
     * Show the modal
     */
    show() {
        return new Promise((resolve, reject) => {
            // Remove any existing modal
            this.destroy();

            // Create modal HTML
            const modalHTML = this.createModalHTML();
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Get modal element
            this.modalElement = document.querySelector('.modal:last-child');
            const modalId = this.modalElement.id;

            // Initialize Bootstrap modal
            this.bootstrapModal = new bootstrap.Modal(this.modalElement, {
                backdrop: this.options.backdrop,
                keyboard: this.options.keyboard
            });

            // Event listeners
            this.setupEventListeners(resolve, reject);

            // Show modal
            this.bootstrapModal.show();

            // Auto close for success messages
            if (this.options.autoClose && this.options.type === 'success') {
                setTimeout(() => {
                    this.hide();
                    resolve(true);
                }, this.options.autoClose);
            }
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners(resolve, reject) {
        const confirmBtn = this.modalElement.querySelector('.confirm-btn');

        // Confirm button click
        confirmBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            if (this.isLoading) return;

            try {
                // Get form data if it's a form modal
                let formData = null;
                if (this.options.type === 'form') {
                    formData = this.getFormData();
                    if (!this.validateForm(formData)) {
                        return;
                    }
                }

                // Show loading state
                this.setLoadingState(true);

                // Execute confirm callback
                const result = await this.onConfirm(formData);

                this.setLoadingState(false);
                this.hide();
                resolve(result);

            } catch (error) {
                this.setLoadingState(false);
                this.showError(error.message || 'An error occurred while processing your request.');
            }
        });

        // Cancel/close events
        this.modalElement.addEventListener('hidden.bs.modal', () => {
            this.destroy();
            resolve(null);
        });

        // Handle escape key if keyboard is enabled
        if (this.options.keyboard) {
            document.addEventListener('keydown', this.handleKeydown.bind(this));
        }
    }

    /**
     * Get form data from modal
     */
    getFormData() {
        const form = this.modalElement.querySelector('.modal-body');
        const formData = new FormData();
        const data = {};

        // Get all form inputs
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                data[input.name] = input.checked;
                formData.append(input.name, input.checked);
            } else {
                data[input.name] = input.value;
                formData.append(input.name, input.value);
            }
        });

        return { formData, data };
    }

    /**
     * Validate form data
     */
    validateForm(formData) {
        const form = this.modalElement.querySelector('.modal-body');
        const requiredInputs = form.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            const value = input.type === 'checkbox' ? input.checked : input.value.trim();

            if (!value) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    /**
     * Set loading state
     */
    setLoadingState(loading) {
        this.isLoading = loading;
        const loadingState = this.modalElement.querySelector('.loading-state');
        const mainContent = this.modalElement.querySelector('.main-content');
        const confirmBtn = this.modalElement.querySelector('.confirm-btn');
        const btnText = confirmBtn.querySelector('.btn-text');
        const spinner = confirmBtn.querySelector('.spinner-border');

        if (loading) {
            loadingState.classList.remove('d-none');
            mainContent.style.opacity = '0.5';
            confirmBtn.disabled = true;
            btnText.textContent = 'Processing...';
            spinner.classList.remove('d-none');
        } else {
            loadingState.classList.add('d-none');
            mainContent.style.opacity = '1';
            confirmBtn.disabled = false;
            btnText.textContent = this.options.confirmText;
            spinner.classList.add('d-none');
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        const errorContainer = this.modalElement.querySelector('.error-container');
        const errorMessage = this.modalElement.querySelector('.error-message');

        errorMessage.textContent = message;
        errorContainer.classList.remove('d-none');

        // Scroll to error
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Hide error message
     */
    hideError() {
        const errorContainer = this.modalElement.querySelector('.error-container');
        errorContainer.classList.add('d-none');
    }

    /**
     * Handle keyboard events
     */
    handleKeydown(e) {
        if (e.key === 'Escape' && this.options.keyboard) {
            this.hide();
        }
    }

    /**
     * Hide the modal
     */
    hide() {
        if (this.bootstrapModal) {
            this.bootstrapModal.hide();
        }
    }

    /**
     * Destroy the modal
     */
    destroy() {
        if (this.modalElement) {
            this.modalElement.remove();
        }
        if (this.bootstrapModal) {
            this.bootstrapModal.dispose();
        }
        document.removeEventListener('keydown', this.handleKeydown);
    }
}

/**
 * Enhanced API Client with Better Error Handling
 */
class APIClient {
    constructor(options = {}) {
        this.baseURL = options.baseURL || '';
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            ...options.headers
        };
        this.timeout = options.timeout || 30000; // 30 seconds
        this.retryAttempts = options.retryAttempts || 2;
        this.retryDelay = options.retryDelay || 1000; // 1 second
    }

    /**
     * Make HTTP request with retry logic
     */
    async request(url, options = {}) {
        const config = {
            method: 'GET',
            headers: { ...this.defaultHeaders, ...options.headers },
            ...options
        };

        // Add timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeout);
        config.signal = controller.signal;

        let lastError;

        for (let attempt = 0; attempt <= this.retryAttempts; attempt++) {
            try {
                if (attempt > 0) {
                    await this.delay(this.retryDelay * attempt);
                }

                const response = await fetch(this.baseURL + url, config);
                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new APIError(
                        `HTTP ${response.status}: ${response.statusText}`,
                        response.status,
                        await this.parseErrorResponse(response)
                    );
                }

                return await this.parseResponse(response);

            } catch (error) {
                lastError = error;

                // Don't retry on certain errors
                if (this.shouldNotRetry(error) || attempt === this.retryAttempts) {
                    clearTimeout(timeoutId);
                    throw this.handleError(error);
                }
            }
        }

        throw this.handleError(lastError);
    }

    /**
     * Parse response based on content type
     */
    async parseResponse(response) {
        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        }

        return await response.text();
    }

    /**
     * Parse error response
     */
    async parseErrorResponse(response) {
        try {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            return await response.text();
        } catch {
            return null;
        }
    }

    /**
     * Determine if we should not retry
     */
    shouldNotRetry(error) {
        if (error instanceof APIError) {
            // Don't retry client errors (4xx) except 408, 429
            return error.status >= 400 && error.status < 500 &&
                   error.status !== 408 && error.status !== 429;
        }

        // Don't retry abort errors
        return error.name === 'AbortError';
    }

    /**
     * Handle and transform errors
     */
    handleError(error) {
        if (error.name === 'AbortError') {
            return new APIError('Request timeout. Please try again.', 408);
        }

        if (error instanceof APIError) {
            return error;
        }

        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            return new APIError('Network error. Please check your connection.', 0);
        }

        return new APIError(error.message || 'An unexpected error occurred.', 500);
    }

    /**
     * Delay helper for retry logic
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // HTTP method helpers
    async get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    }

    async post(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'POST',
            body: data instanceof FormData ? data : JSON.stringify(data)
        });
    }

    async put(url, data, options = {}) {
        return this.request(url, {
            ...options,
            method: 'PUT',
            body: data instanceof FormData ? data : JSON.stringify(data)
        });
    }

    async delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
}

/**
 * Custom API Error class
 */
class APIError extends Error {
    constructor(message, status, details = null) {
        super(message);
        this.name = 'APIError';
        this.status = status;
        this.details = details;
    }
}

/**
 * Global API client instance
 */
const apiClient = new APIClient();

/**
 * Utility functions for the task management system
 */
const TaskManager = {
    // Job Management
    async assignJob(jobId, assignmentData) {
        return ModernModal.confirm({
            title: 'Assign Job',
            type: 'form',
            confirmText: 'Assign Job',
            formFields: [
                {
                    type: 'select',
                    name: 'assigned_user_id',
                    label: 'Assign To',
                    required: true,
                    options: assignmentData.users || []
                },
                {
                    type: 'select',
                    name: 'priority',
                    label: 'Priority',
                    required: true,
                    value: '2',
                    options: [
                        { value: '1', text: 'High' },
                        { value: '2', text: 'Medium' },
                        { value: '3', text: 'Low' },
                        { value: '4', text: 'Very Low' }
                    ]
                },
                {
                    type: 'date',
                    name: 'due_date',
                    label: 'Due Date',
                    required: false
                },
                {
                    type: 'textarea',
                    name: 'assignment_notes',
                    label: 'Assignment Notes',
                    placeholder: 'Add any special instructions...'
                }
            ],
            onConfirm: async (formData) => {
                const response = await apiClient.post(`/supervisor/jobs/${jobId}/assign`, formData.data);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to assign job');
                }

                return response;
            }
        });
    },

    async completeTask(taskId, taskName) {
        return ModernModal.confirm({
            title: 'Complete Task',
            message: `Complete the task: "${taskName}"?`,
            type: 'form',
            confirmText: 'Complete Task',
            confirmClass: 'btn-success',
            formFields: [
                {
                    type: 'textarea',
                    name: 'completion_notes',
                    label: 'Completion Notes',
                    placeholder: 'Describe what was completed...',
                    required: true
                }
            ],
            onConfirm: async (formData) => {
                const response = await apiClient.post(`/tasks/${taskId}/complete`, formData.data);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to complete task');
                }

                return response;
            }
        });
    },

    async startTask(taskId, taskName) {
        return ModernModal.confirm({
            title: 'Start Task',
            message: `Start the task: "${taskName}"?\n\nThis will mark it as "In Progress".`,
            confirmText: 'Start Task',
            confirmClass: 'btn-primary',
            onConfirm: async () => {
                const response = await apiClient.post(`/tasks/${taskId}/start`);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to start task');
                }

                return response;
            }
        });
    },

    async approveJob(jobId) {
        return ModernModal.confirm({
            title: 'Approve Job',
            type: 'form',
            confirmText: 'Submit Approval',
            confirmClass: 'btn-success',
            formFields: [
                {
                    type: 'select',
                    name: 'action',
                    label: 'Action',
                    required: true,
                    value: 'approve',
                    options: [
                        { value: 'approve', text: 'Approve' },
                        { value: 'reject', text: 'Reject' }
                    ]
                },
                {
                    type: 'textarea',
                    name: 'approval_notes',
                    label: 'Notes',
                    placeholder: 'Add approval/rejection notes...'
                }
            ],
            onConfirm: async (formData) => {
                const response = await apiClient.post(`/engineer/jobs/${jobId}/approve`, formData.data);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to process approval');
                }

                return response;
            }
        });
    },

    
    async completeJob(jobId) {
        return ModernModal.confirm({
            title: 'Complete Job',
            type: 'form',
            confirmText: 'Complete Job',
            confirmClass: 'btn-success',
            formFields: [
                {
                    type: 'checkbox',
                    name: 'is_minor_issue',
                    label: 'This is a minor issue (complete without items/approval)'
                },
                {
                    type: 'textarea',
                    name: 'completion_notes',
                    label: 'Completion Notes',
                    placeholder: 'Describe what was completed or the issue resolved...',
                    required: true
                }
            ],
            onConfirm: async (formData) => {
                const response = await apiClient.post(`/technicalofficer/jobs/${jobId}/complete`, formData.data);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to complete job');
                }

                return response;
            }
        });
    },

    // Extension Management
    async requestExtension(taskId, currentDeadline) {
        return ModernModal.confirm({
            title: 'Request Task Extension',
            type: 'form',
            confirmText: 'Request Extension',
            formFields: [
                {
                    type: 'date',
                    name: 'new_deadline',
                    label: 'New Deadline',
                    required: true,
                    value: currentDeadline
                },
                {
                    type: 'textarea',
                    name: 'reason',
                    label: 'Reason for Extension',
                    placeholder: 'Explain why you need more time...',
                    required: true
                }
            ],
            onConfirm: async (formData) => {
                const response = await apiClient.post(`/tasks/${taskId}/request-extension`, formData.data);

                if (response.success) {
                    await ModernModal.success(response.message);
                    this.refreshPage();
                } else {
                    throw new Error(response.message || 'Failed to request extension');
                }

                return response;
            }
        });
    },

    // Utility functions
    refreshPage() {
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    },

    // Show success message
    showSuccess(message) {
        return ModernModal.success(message);
    },

    // Show error message
    showError(message) {
        return ModernModal.error(message);
    }
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ModernModal, APIClient, TaskManager };
}
