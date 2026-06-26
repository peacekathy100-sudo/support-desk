@extends('layouts.client')

@section('content')
<style>
    .ticket-create-header {
        background: linear-gradient(135deg, #3496D7 0%, #2980BB 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgba(52, 150, 215, 0.2);
    }

    .ticket-create-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .ticket-create-header p {
        opacity: 0.95;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #3496D7;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
        border-bottom: 2px solid #e8f3ff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        font-size: 1rem;
    }

    .form-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-group-row {
            grid-template-columns: 1fr;
        }
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .form-label i {
        color: #3496D7;
        font-size: 0.9rem;
    }

    .required-badge {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: #FF4444;
        border-radius: 50%;
        margin-left: 0.3rem;
    }

    .form-control,
    .form-select {
        border: 1.5px solid #e0e8f0;
        border-radius: 8px;
        padding: 0.7rem 0.9rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3496D7;
        box-shadow: 0 0 0 3px rgba(52, 150, 215, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #a0adb8;
    }

    .form-hint {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #666;
    }

    .form-hint i {
        color: #3496D7;
        font-size: 0.8rem;
    }

    .card {
        border: 1px solid #e8f3ff;
        box-shadow: 0 4px 12px rgba(52, 150, 215, 0.08);
        border-radius: 12px;
    }

    .card-body {
        padding: 2rem;
    }

    .alert {
        border-radius: 8px;
        border-left: 4px solid #FF4444;
        margin-bottom: 1.5rem;
    }

    .alert-heading {
        color: #d32f2f;
        margin-bottom: 0.8rem;
        font-weight: 600;
    }

    /* File Upload */
    .file-upload-box {
        border: 2px dashed #3496D7;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fbff;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-box:hover {
        background: #e8f3ff;
        border-color: #2980BB;
    }

    .file-upload-box i {
        color: #3496D7;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    .file-upload-box p {
        margin: 0.5rem 0 0;
        color: #666;
    }

    .file-upload-formats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.8rem;
        justify-content: center;
    }

    .format-badge {
        display: inline-block;
        background: white;
        border: 1px solid #3496D7;
        color: #3496D7;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .progress {
        height: 6px;
        border-radius: 4px;
        background: #e8f3ff;
        margin-bottom: 0.5rem;
    }

    .progress-bar {
        background: linear-gradient(90deg, #3496D7, #2980BB);
        border-radius: 4px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        border-top: 1px solid #e8f3ff;
        padding-top: 1.5rem;
        margin-top: 2rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #3496D7, #2980BB);
        color: white;
        border: none;
        padding: 0.7rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .btn-submit:hover {
        box-shadow: 0 6px 16px rgba(52, 150, 215, 0.3);
        transform: translateY(-2px);
        color: white;
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-cancel {
        background: white;
        color: #666;
        border: 1.5px solid #e0e8f0;
        padding: 0.7rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-cancel:hover {
        border-color: #3496D7;
        color: #3496D7;
        background: #f8fbff;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #3496D7;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        color: #2980BB;
        gap: 0.8rem;
    }
</style>

<div class="container py-4">
    <a href="{{ route('client.tickets.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to My Tickets
    </a>

    <!-- Header -->
    <div class="ticket-create-header">
        <h1><i class="bi bi-ticket-detailed" style="margin-right: 0.5rem;"></i>Create Support Ticket</h1>
        <p>Submit a new support request to our team. We'll get back to you as soon as possible.</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <i class="bi bi-exclamation-circle"></i> Please fix the following errors:
                    </h6>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('client.tickets.store') }}" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Issue Details Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-info-circle"></i> Issue Details
                    </div>

                    <!-- Subject -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-chat-left-text"></i> Ticket Subject
                            <span class="required-badge"></span>
                        </label>
                        <input 
                            name="subject" 
                            class="form-control @error('subject') is-invalid @enderror" 
                            value="{{ old('subject') }}" 
                            placeholder="e.g., Login page not loading"
                            required 
                            maxlength="255">
                        @error('subject')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i> Describe your issue in a few words (max 255 characters)
                        </div>
                    </div>

                    <!-- Category & Priority Row -->
                    <div class="form-group-row">
                        <!-- Category -->
                        <div>
                            <label class="form-label">
                                <i class="bi bi-tag"></i> Category
                            </label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Select Category --</option>
                                @forelse($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>No categories available</option>
                                @endforelse
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-hint">
                                <i class="bi bi-lightning"></i> Helps us route to the right team
                            </div>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="form-label">
                                <i class="bi bi-exclamation-triangle"></i> Priority Level
                            </label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                                <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>
                                    🟢 Low - Non-urgent issue
                                </option>
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>
                                    🟡 Normal - Standard support
                                </option>
                                <option value="high" {{ old('priority', 'normal') === 'high' ? 'selected' : '' }}>
                                    🟠 High - Important issue
                                </option>
                                <option value="urgent" {{ old('priority', 'normal') === 'urgent' ? 'selected' : '' }}>
                                    🔴 Urgent - Critical issue
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-hint">
                                <i class="bi bi-clock"></i> Affects response time
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-chat-dots"></i> Detailed Description
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-file-text"></i> Description
                            <span class="required-badge"></span>
                        </label>
                        <textarea 
                            name="description" 
                            rows="6" 
                            class="form-control @error('description') is-invalid @enderror" 
                            placeholder="Provide detailed information about your issue:&#10;• What were you trying to do?&#10;• What happened instead?&#10;• When did this start?&#10;• Any error messages?"
                            required
                            maxlength="5000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i> The more details you provide, the faster we can help (max 5000 characters)
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-paperclip"></i> Attachments (Optional)
                    </div>

                    <div class="file-upload-box" onclick="document.querySelector('input[name=\"attachments[]\"]').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <p style="font-weight: 600; margin-top: 0.5rem;">Click to upload or drag files here</p>
                        <p style="margin-top: 0.3rem;">Include screenshots, logs, or documents to help us understand</p>
                        <div class="file-upload-formats">
                            <span class="format-badge">JPG</span>
                            <span class="format-badge">PNG</span>
                            <span class="format-badge">PDF</span>
                            <span class="format-badge">DOC</span>
                            <span class="format-badge">XLSX</span>
                            <span class="format-badge">TXT</span>
                        </div>
                    </div>

                    <input 
                        name="attachments[]" 
                        type="file" 
                        class="d-none @error('attachments') is-invalid @enderror" 
                        multiple 
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                        id="fileInput">

                    @error('attachments')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror

                    <div class="form-hint mt-2">
                        <i class="bi bi-info-circle"></i> Maximum 10MB per file • Supported formats: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX, TXT
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div id="uploadProgress" class="mb-3 d-none">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-hourglass-split"></i> Uploading files...
                    </small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('client.tickets.index') }}" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="bi bi-send"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const fileInput = document.querySelector('input[name="attachments[]"]');
        const uploadBox = document.querySelector('.file-upload-box');
        const uploadProgress = document.getElementById('uploadProgress');
        const submitBtn = document.getElementById('submitBtn');

        // File upload drag and drop
        if (uploadBox && fileInput) {
            uploadBox.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadBox.style.background = '#e8f3ff';
                uploadBox.style.borderColor = '#2980BB';
            });

            uploadBox.addEventListener('dragleave', () => {
                uploadBox.style.background = '#f8fbff';
                uploadBox.style.borderColor = '#3496D7';
            });

            uploadBox.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadBox.style.background = '#f8fbff';
                uploadBox.style.borderColor = '#3496D7';
                fileInput.files = e.dataTransfer.files;
            });
        }

        // File size validation
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const maxSize = 10 * 1024 * 1024; // 10MB
                let hasError = false;

                Array.from(this.files).forEach(file => {
                    if (file.size > maxSize) {
                        hasError = true;
                        alert(`File "${file.name}" exceeds 10MB limit.`);
                    }
                });

                if (hasError) {
                    this.value = '';
                }
            });
        }

        // Form submission with progress indicator
        if (form) {
            form.addEventListener('submit', function(e) {
                if (fileInput && fileInput.files.length > 0) {
                    uploadProgress.classList.remove('d-none');
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
@endsection
