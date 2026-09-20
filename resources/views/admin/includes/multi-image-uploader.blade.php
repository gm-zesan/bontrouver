{{--
| Multi Image & File Uploader Component
| Supports both multi-image gallery uploads and multi-document/file uploads.
| Uses the unified upload-modal component.
|
| Parameters:
| $name - Form field name (default: 'gallery[]' or 'documents[]')
| $instanceId - Unique string identifier for this uploader instance
| $modalId - ID for the modal
| $label - Trigger button label (default: 'Add Photos' or 'Upload Documents')
| $modalTitle - Modal header title
| $helpText - Subtitle / format info
| $accept - Accepted file types (e.g. 'image/*' or '.pdf,.doc,.docx')
| $fileType - 'image' | 'document' | 'file' (default: 'image')
| $maxSizeMb - Max file size in MB (default: 5 for images, 10 for docs)
| $applyBtnText - Modal confirm button text
--}}
@php
    use Illuminate\Support\Str;

    $fileType = $fileType ?? 'image';
    $isDocument = in_array($fileType, ['document', 'file', 'doc', 'pdf']);
    $name = $name ?? ($isDocument ? 'documents[]' : 'gallery[]');
    $label = $label ?? ($isDocument ? 'Upload Documents' : 'Add Photos');
    $modalTitle = $modalTitle ?? ($isDocument ? 'Upload Documents' : 'Upload Gallery Photos');
    $maxSizeMb = $maxSizeMb ?? ($isDocument ? 10 : 5);

    $defaultHelpText = $isDocument
        ? 'PDF, DOC, DOCX, XLS, TXT up to ' . $maxSizeMb . 'MB each'
        : 'PNG, JPG, WebP up to ' . $maxSizeMb . 'MB each';

    $helpText = $helpText ?? $defaultHelpText;

    $defaultAccept = $isDocument
        ? '.pdf,.doc,.docx,.xls,.xlsx,.txt'
        : 'image/png,image/jpeg,image/webp,image/jpg,image/svg+xml';

    $accept = $accept ?? $defaultAccept;
    $icon = $icon ?? ($isDocument ? 'ri-file-upload-line' : 'ri-gallery-upload-line');

    if (empty($instanceId)) {
        $instanceId = 'mi_' . Str::random(8);
    }
    if (empty($modalId)) {
        $modalId = 'miModal_' . $instanceId;
    }
@endphp

<div class="admin-multi-uploader" id="multiUploader_{{ $instanceId }}" data-instance-id="{{ $instanceId }}"
    data-modal-id="{{ $modalId }}" data-file-type="{{ $fileType }}" data-max-size="{{ $maxSizeMb }}">

    {{-- Real Hidden File Input Submitted With The Form --}}
    <input type="file" id="multiInput_{{ $instanceId }}" name="{{ $name }}" class="d-none actual-multi-input"
        accept="{{ $accept }}" multiple>

    {{-- Upload Button Trigger --}}
    <button type="button" class="btn-upload-trigger w-100"
            data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
        <div class="upload-trigger-icon">
            <i class="{{ $icon }}"></i>
        </div>
        <div class="upload-trigger-info">
            <span class="upload-trigger-title">{{ $label }}</span>
            <span class="upload-trigger-sub">{{ $helpText }}</span>
        </div>
        <div class="upload-trigger-btn">
            <i class="{{ $isDocument ? 'ri-upload-2-line' : 'ri-image-add-line' }}"></i>
            <span>Browse</span>
        </div>
    </button>

    {{-- Applied Items Container on the Page --}}
    <div class="{{ $isDocument ? 'applied-doc-list d-flex flex-column gap-2 mt-2' : 'row g-2 mt-2' }}"
        id="appliedGrid_{{ $instanceId }}"></div>

    @error(str_replace('[]', '.*', $name))
        <div class="error_msg mt-2">{{ $message }}</div>
    @enderror
</div>

{{-- UNIFIED UPLOAD MODAL COMPONENT --}}
@include('admin.includes.upload-modal', [
    'modalId' => $modalId,
    'modalTitle' => $modalTitle,
    'helpText' => $helpText,
    'mode' => 'multiple',
    'fileType' => $fileType,
    'accept' => $accept,
    'maxSizeMb' => $maxSizeMb,
    'icon' => $icon,
    'applyBtnText' => $applyBtnText ?? ($isDocument ? 'Apply Documents' : 'Apply Photos'),
    'instanceId' => $instanceId,
])

@once
    @push('custom-script')
        <script>
            (function () {
                'use strict';
                function initUnifiedMultiUploaders() {
                    document.querySelectorAll('.admin-multi-uploader').forEach(function (uploader) {
                        if (uploader.dataset.initialized === 'true') return;
                        uploader.dataset.initialized = 'true';

                        var iid = uploader.dataset.instanceId,
                            modalId = uploader.dataset.modalId,
                            fileType = uploader.dataset.fileType || 'image',
                            isDoc = (fileType === 'document' || fileType === 'file' || fileType === 'doc' || fileType === 'pdf'),
                            maxSizeMb = parseFloat(uploader.dataset.maxSize || (isDoc ? '10' : '5'));

                        var actualInput = document.getElementById('multiInput_' + iid),
                            counterBadge = document.getElementById('multiCounter_' + iid),
                            appliedGridEl = document.getElementById('appliedGrid_' + iid),
                            modalEl = document.getElementById(modalId);

                        if (!actualInput || !modalEl) return;

                        var dropzoneEl = document.getElementById('dropzone_' + iid),
                            modalPickerEl = document.getElementById('modalPicker_' + iid),
                            modalBrowseBtn = document.getElementById('modalBrowseBtn_' + iid),
                            stagedCont = document.getElementById('modalStagedContainer_' + iid),
                            stagedGrid = document.getElementById('modalStagedGrid_' + iid),
                            stagedCnt = document.getElementById('modalStagedCount_' + iid),
                            clearAllBtn = document.getElementById('modalClearAll_' + iid),
                            applyBtn = document.getElementById('modalApplyBtn_' + iid);

                        var stagedFiles = [], appliedFiles = [];

                        function fmtBytes(b) {
                            if (!b || b === 0) return '0 B';
                            var k = 1024, s = ['B', 'KB', 'MB', 'GB'], i = Math.floor(Math.log(b) / Math.log(k));
                            return (b / Math.pow(k, i)).toFixed(1) + ' ' + s[i];
                        }

                        function getFileIconClass(filename) {
                            var ext = (filename || '').split('.').pop().toLowerCase();
                            if (ext === 'pdf') return 'ri-file-pdf-line text-danger';
                            if (ext === 'doc' || ext === 'docx') return 'ri-file-word-line text-primary';
                            if (ext === 'xls' || ext === 'xlsx' || ext === 'csv') return 'ri-file-excel-line text-success';
                            if (ext === 'txt') return 'ri-file-text-line text-secondary';
                            if (ext === 'zip' || ext === 'rar' || ext === '7z') return 'ri-file-zip-line text-warning';
                            return 'ri-file-line text-muted';
                        }

                        function syncInput() {
                            try {
                                var dt = new DataTransfer();
                                appliedFiles.forEach(function (f) { dt.items.add(f); });
                                actualInput.files = dt.files;
                            } catch (e) {
                                console.warn('DataTransfer error:', e);
                            }
                            if (window.jQuery) {
                                window.jQuery(actualInput).trigger('change');
                            } else {
                                actualInput.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }

                        function updateCounter() {
                            if (counterBadge) {
                                counterBadge.textContent = appliedFiles.length + (isDoc ? ' Files' : ' Selected');
                            }
                        }

                        function renderStaged() {
                            if (!stagedGrid) return;
                            stagedGrid.innerHTML = '';
                            if (stagedFiles.length === 0) {
                                if (stagedCont) stagedCont.classList.add('d-none');
                                if (applyBtn) applyBtn.disabled = true;
                                return;
                            }
                            if (stagedCont) stagedCont.classList.remove('d-none');
                            if (stagedCnt) stagedCnt.textContent = stagedFiles.length;
                            if (applyBtn) applyBtn.disabled = false;

                            stagedFiles.forEach(function (file, idx) {
                                var isImg = file.type && file.type.startsWith('image/');
                                var col = document.createElement('div');
                                col.className = isDoc ? 'col-12' : 'col-6 col-md-3';
                                col.id = 'mst_' + iid + '_' + idx;

                                if (isImg) {
                                    var reader = new FileReader();
                                    reader.onload = (function (currentIndex, currentFile, currentCol) {
                                        return function (ev) {
                                            currentCol.innerHTML = '<div class="multi-photo-card">'
                                                + '<button type="button" class="multi-photo-remove-btn multi-modal-rm" data-idx="' + currentIndex + '" data-iid="' + iid + '" title="Remove"><i class="ri-close-line"></i></button>'
                                                + '<img src="' + ev.target.result + '" class="multi-photo-thumb" alt="' + currentFile.name + '">'
                                                + '<div class="multi-photo-info"><span class="multi-photo-name" title="' + currentFile.name + '">' + currentFile.name + '</span>'
                                                + '<span class="multi-photo-badge new-badge">' + fmtBytes(currentFile.size) + '</span></div></div>';
                                        };
                                    })(idx, file, col);
                                    reader.readAsDataURL(file);
                                } else {
                                    var iconClass = getFileIconClass(file.name);
                                    col.innerHTML = '<div class="multi-doc-card d-flex align-items-center justify-content-between p-2 rounded border bg-white">'
                                        + '<div class="d-flex align-items-center text-truncate me-2">'
                                        + '<i class="' + iconClass + ' me-2" style="font-size: 22px;"></i>'
                                        + '<div class="text-truncate"><span class="d-block fw-semibold text-dark text-truncate" style="font-size: 12px;" title="' + file.name + '">' + file.name + '</span>'
                                        + '<span class="text-muted" style="font-size: 11px;">' + fmtBytes(file.size) + '</span></div></div>'
                                        + '<button type="button" class="btn btn-sm btn-outline-danger p-0 multi-modal-rm flex-shrink-0" data-idx="' + idx + '" data-iid="' + iid + '" style="width: 26px; height: 26px; line-height: 1;" title="Remove"><i class="ri-delete-bin-line" style="font-size: 13px;"></i></button>'
                                        + '</div>';
                                }
                                stagedGrid.appendChild(col);
                            });
                        }

                        function renderApplied() {
                            if (!appliedGridEl) return;
                            appliedGridEl.innerHTML = '';
                            appliedFiles.forEach(function (file, idx) {
                                var isImg = file.type && file.type.startsWith('image/');
                                var col = document.createElement('div');
                                col.className = isDoc ? 'w-100' : 'col-6 col-md-4 col-lg-3';
                                col.id = 'app_' + iid + '_' + idx;

                                if (isImg) {
                                    var reader = new FileReader();
                                    reader.onload = (function (currentIndex, currentFile, currentCol) {
                                        return function (ev) {
                                            currentCol.innerHTML = '<div class="multi-photo-card multi-photo-new">'
                                                + '<button type="button" class="multi-photo-remove-btn multi-applied-rm" data-idx="' + currentIndex + '" data-iid="' + iid + '" title="Remove"><i class="ri-close-line"></i></button>'
                                                + '<img src="' + ev.target.result + '" class="multi-photo-thumb" alt="' + currentFile.name + '">'
                                                + '<div class="multi-photo-info"><span class="multi-photo-name" title="' + currentFile.name + '">' + currentFile.name + '</span>'
                                                + '<span class="multi-photo-badge new-badge">New &middot; ' + fmtBytes(currentFile.size) + '</span></div></div>';
                                        };
                                    })(idx, file, col);
                                    reader.readAsDataURL(file);
                                } else {
                                    var iconClass = getFileIconClass(file.name);
                                    col.innerHTML = '<div class="multi-doc-card d-flex align-items-center justify-content-between p-2 rounded border bg-white shadow-sm" style="border-color: #f95716 !important;">'
                                        + '<div class="d-flex align-items-center text-truncate me-2">'
                                        + '<i class="' + iconClass + ' me-2" style="font-size: 22px;"></i>'
                                        + '<div class="text-truncate"><span class="d-block fw-semibold text-dark text-truncate" style="font-size: 12px;" title="' + file.name + '">' + file.name + '</span>'
                                        + '<span class="badge" style="background:rgba(249,87,22,0.1);color:#f95716;font-size:10px;">New &middot; ' + fmtBytes(file.size) + '</span></div></div>'
                                        + '<button type="button" class="btn btn-sm btn-outline-danger p-0 multi-applied-rm flex-shrink-0" data-idx="' + idx + '" data-iid="' + iid + '" style="width: 26px; height: 26px; line-height: 1;" title="Remove"><i class="ri-delete-bin-line" style="font-size: 13px;"></i></button>'
                                        + '</div>';
                                }
                                appliedGridEl.appendChild(col);
                            });
                            updateCounter();
                            syncInput();
                        }

                        function addToStaged(files) {
                            for (var i = 0; i < files.length; i++) {
                                var f = files[i];
                                if (!isDoc && !f.type.match('image.*')) {
                                    if (window.toastr) toastr.warning(f.name + ' is not an image file');
                                    continue;
                                }
                                if (f.size > maxSizeMb * 1024 * 1024) {
                                    if (window.toastr) toastr.warning(f.name + ' exceeds ' + maxSizeMb + 'MB limit');
                                    continue;
                                }
                                stagedFiles.push(f);
                            }
                            renderStaged();
                        }

                        // Clicking ANYWHERE in the dropzone area triggers the file picker
                        if (dropzoneEl) {
                            dropzoneEl.addEventListener('click', function (e) {
                                if (modalPickerEl) modalPickerEl.click();
                            });
                            dropzoneEl.addEventListener('dragover', function (e) {
                                e.preventDefault();
                                this.classList.add('dragover');
                            });
                            dropzoneEl.addEventListener('dragleave', function () {
                                this.classList.remove('dragover');
                            });
                            dropzoneEl.addEventListener('drop', function (e) {
                                e.preventDefault();
                                this.classList.remove('dragover');
                                if (e.dataTransfer && e.dataTransfer.files.length) {
                                    addToStaged(e.dataTransfer.files);
                                }
                            });
                        }

                        if (modalPickerEl) {
                            modalPickerEl.addEventListener('change', function () {
                                if (this.files && this.files.length) {
                                    addToStaged(this.files);
                                    this.value = '';
                                }
                            });
                        }

                        if (clearAllBtn) {
                            clearAllBtn.addEventListener('click', function () {
                                stagedFiles = [];
                                renderStaged();
                            });
                        }

                        document.addEventListener('click', function (e) {
                            var btnModal = e.target.closest('.multi-modal-rm[data-iid="' + iid + '"]');
                            if (btnModal) {
                                stagedFiles.splice(parseInt(btnModal.dataset.idx, 10), 1);
                                renderStaged();
                                return;
                            }
                            var btnApplied = e.target.closest('.multi-applied-rm[data-iid="' + iid + '"]');
                            if (btnApplied) {
                                appliedFiles.splice(parseInt(btnApplied.dataset.idx, 10), 1);
                                renderApplied();
                                return;
                            }
                        });

                        if (applyBtn) {
                            applyBtn.addEventListener('click', function () {
                                stagedFiles.forEach(function (f) { appliedFiles.push(f); });
                                stagedFiles = [];
                                renderApplied();
                                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                    var m = bootstrap.Modal.getInstance(modalEl);
                                    if (m) m.hide();
                                } else if (window.jQuery) {
                                    window.jQuery(modalEl).modal('hide');
                                }
                            });
                        }

                        modalEl.addEventListener('hidden.bs.modal', function () {
                            stagedFiles = [];
                            renderStaged();
                        });
                    });
                }

                window.initUnifiedMultiUploaders = initUnifiedMultiUploaders;

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initUnifiedMultiUploaders);
                } else {
                    initUnifiedMultiUploaders();
                }
            })();
        </script>
    @endpush
@endonce