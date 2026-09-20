{{--
| Unified Upload Modal Component
| Single reusable modal for all upload types (Single Image, Multi-Image Gallery, Documents & Files)
|
| Parameters:
| $modalId - (required) DOM ID for modal (e.g. 'imageUploadModal_image', 'miModal_abc123')
| $modalTitle - Modal header title (default: 'Upload Files')
| $helpText - Modal header subtitle/help text
| $icon - Icon class for header badge & dropzone (default based on mode/fileType)
| $mode - 'single' | 'multiple' (default: 'single')
| $fileType - 'image' | 'document' | 'file' (default: 'image')
| $accept - Accepted mime types / extensions (e.g. 'image/*', '.pdf,.doc,.docx')
| $maxSizeMb - Maximum allowed file size in MB (default: 10 for docs, 5 for images)
| $showNameInput - Boolean: whether to show asset name input field in single mode (default: false)
| $nameLabel - Label for asset name input
| $namePlaceholder - Placeholder for asset name input
| $currentName - Current name value
| $shape - 'rectangle' | 'circle' (for single image preview)
| $applyBtnText - Text on confirm button
| $instanceId - Optional unique ID suffix for DOM selectors
--}}
@php
    $mode = $mode ?? 'single';
    $fileType = $fileType ?? 'image';
    $isMultiple = ($mode === 'multiple');
    $isDocument = in_array($fileType, ['document', 'file', 'doc', 'pdf']);
    $maxSizeMb = $maxSizeMb ?? ($isDocument ? 10 : 5);

    if (empty($icon)) {
        if ($isDocument) {
            $icon = 'ri-file-upload-line';
            $dropzoneIcon = 'ri-file-add-line';
        } elseif ($isMultiple) {
            $icon = 'ri-gallery-upload-line';
            $dropzoneIcon = 'ri-gallery-upload-line';
        } else {
            $icon = 'ri-upload-cloud-2-line';
            $dropzoneIcon = 'ri-image-add-line';
        }
    } else {
        $dropzoneIcon = $icon;
    }

    $defaultTitle = $isDocument
        ? ($isMultiple ? 'Upload Documents' : 'Upload Document')
        : ($isMultiple ? 'Upload Gallery Photos' : 'Upload Image');

    $modalTitle = $modalTitle ?? $defaultTitle;

    $defaultHelpText = $isDocument
        ? 'Supports PDF, DOC, DOCX, XLS, TXT up to ' . $maxSizeMb . 'MB' . ($isMultiple ? ' each' : '')
        : 'Supports JPG, PNG, WebP, SVG up to ' . $maxSizeMb . 'MB' . ($isMultiple ? ' each' : '');

    $helpText = $helpText ?? $defaultHelpText;

    $defaultAccept = $isDocument
        ? '.pdf,.doc,.docx,.xls,.xlsx,.txt'
        : 'image/jpeg,image/png,image/webp,image/jpg,image/svg+xml';

    $accept = $accept ?? $defaultAccept;

    $defaultApplyText = $isDocument
        ? ($isMultiple ? 'Apply Documents' : 'Apply Document')
        : ($isMultiple ? 'Apply Photos' : 'Apply Image');

    $applyBtnText = $applyBtnText ?? $defaultApplyText;
    $domId = $instanceId ?? $modalId;
@endphp

<div class="modal fade image-upload-modal unified-upload-modal" id="{{ $modalId }}" tabindex="-1"
    aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            {{-- Modal Header --}}
            <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                        style="width: 34px; height: 34px; background-color: #f95716;">
                        <i class="{{ $icon }}" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="{{ $modalId }}Label"
                            style="font-size: 15.5px;">
                            {{ $modalTitle }}
                        </h5>
                        <span class="text-muted" style="font-size: 11.5px;">{{ $helpText }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4">

                {{-- 1. Optional Name Field (Single Mode) --}}
                @if(!$isMultiple && !empty($showNameInput))
                    <div class="mb-3">
                        <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 13px;">
                            {{ $nameLabel ?? ($isDocument ? 'Document Name' : 'Image Name') }}
                        </label>
                        <input type="text" class="form-control custom-input modal-image-name-input"
                            placeholder="{{ $namePlaceholder ?? ($isDocument ? 'e.g. Structural Blueprint v2' : 'e.g. Modern Architecture Facade') }}"
                            value="{{ $currentName ?? '' }}" style="height: 38px; font-size: 13px;">
                        <span class="text-muted d-block mt-1" style="font-size: 11.5px;">
                            The name or title for this asset
                        </span>
                    </div>
                @endif

                {{-- 2. Interactive Dropzone Area --}}
                <div class="mb-3">
                    @if(!$isMultiple)
                        <label class="form-label mb-1 fw-semibold text-dark" style="font-size: 13px;">
                            {{ $isDocument ? 'Document File' : 'Image File' }} <span class="text-danger">*</span>
                        </label>
                    @endif

                    {{-- Hidden Modal Picker --}}
                    <input type="file" class="d-none modal-file-picker" id="modalPicker_{{ $domId }}"
                        accept="{{ $accept }}" {{ $isMultiple ? 'multiple' : '' }}>

                    {{-- 2a. Dropzone Empty State (Dashed Area) --}}
                    <div class="dropzone-box modal-dropzone" id="dropzone_{{ $domId }}">
                        <div class="modal-dropzone-empty" id="modalDropzoneEmpty_{{ $domId }}">
                            <div class="dropzone-icon">
                                <i class="{{ $dropzoneIcon }}"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">
                                Drag &amp; drop
                                {{ $isDocument ? 'documents / files' : ($isMultiple ? 'photos' : 'image') }} here
                            </h6>
                            <p class="text-muted mb-3" style="font-size: 12px;">
                                {{ $isMultiple ? 'Hold Ctrl / Cmd to select multiple files' : $helpText }}
                            </p>
                            <button type="button" class="btn btn-sm upload-btn px-4 btn-modal-browse mx-auto"
                                id="modalBrowseBtn_{{ $domId }}">
                                <i class="ri-folder-open-line me-1"></i> Browse
                                {{ $isDocument ? 'Files' : ($isMultiple ? 'Photos' : 'File') }}
                            </button>
                        </div>
                    </div>

                    {{-- 2b. Staged State for SINGLE mode (Modern Showcase Card) --}}
                    @if(!$isMultiple)
                        <div class="modal-dropzone-staged modal-single-preview-card d-none"
                            id="modalDropzoneStaged_{{ $domId }}">
                            <div
                                class="single-preview-viewport position-relative overflow-hidden {{ ($shape ?? 'rectangle') === 'circle' ? 'circle-viewport' : '' }}">
                                <img src="" alt="Staged Preview" class="modal-staged-img">

                                {{-- Status Pill Top Left --}}
                                <div class="single-preview-status-pill">
                                    <i class="ri-checkbox-circle-fill"></i>
                                    <span>Selected Image</span>
                                </div>

                                {{-- Clear/Remove Button Top Right --}}
                                <button type="button" class="btn-single-modal-clear" title="Remove selected image">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>

                            {{-- Meta & Replace Actions Footer --}}
                            <div class="single-preview-meta-bar">
                                <div class="d-flex align-items-center gap-2 min-w-0 me-2">
                                    <div class="file-icon-box flex-shrink-0">
                                        <i class="ri-image-2-fill"></i>
                                    </div>
                                    <div class="min-w-0 text-start">
                                        <span class="modal-staged-name fw-bold text-dark d-block text-truncate"
                                            style="font-size: 13px;"></span>
                                        <span class="modal-staged-size text-muted" style="font-size: 11.5px;"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 btn-modal-reselect"
                                        id="modalReselectBtn_{{ $domId }}"
                                        style="font-size: 12px; height: 32px; font-weight: 600; border-radius: 6px;">
                                        <i class="ri-refresh-line me-1"></i> Change Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Staged Files List / Grid for MULTIPLE mode --}}
                    @if($isMultiple)
                        <div id="modalStagedContainer_{{ $domId }}" class="modal-staged-container d-none mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark" style="font-size: 13px;">
                                    <i class="ri-checkbox-circle-line text-success me-1"></i>
                                    Ready to apply (<span id="modalStagedCount_{{ $domId }}">0</span> files)
                                </span>
                                <button type="button"
                                    class="btn btn-sm btn-link text-danger text-decoration-none p-0 btn-modal-clear-all"
                                    id="modalClearAll_{{ $domId }}" style="font-size: 12px;">
                                    <i class="ri-delete-bin-line me-1"></i>Clear All
                                </button>
                            </div>
                            <div class="row g-2 modal-staged-grid" id="modalStagedGrid_{{ $domId }}"
                                style="max-height: 240px; overflow-y: auto; padding-right: 4px;"></div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer px-4 py-3 border-top d-flex justify-content-end gap-2"
                style="background-color: #f8fafc;">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal"
                    style="height: 36px; font-weight: 600;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4 btn-modal-apply"
                    id="modalApplyBtn_{{ $domId }}" data-dom-id="{{ $domId }}"
                    style="height: 36px; font-weight: 600; background-color: #f95716; border-color: #f95716;" disabled>
                    <i class="{{ $isMultiple ? 'ri-check-double-line' : 'ri-check-line' }} me-1"></i>
                    {{ $applyBtnText }}
                </button>
            </div>

        </div>
    </div>
</div>