@php
    $inputId = $id ?? $name;
    $modalId = 'imageUploadModal_' . $inputId;
    $hasImage = !empty($currentImage);
    $shape = $shape ?? 'rectangle'; // 'rectangle' or 'circle'
    $height = $height ?? ($shape === 'circle' ? '130px' : '170px');
    $width = $shape === 'circle' ? $height : '100%';
    $accept = $accept ?? 'image/jpeg,image/png,image/webp,image/jpg,image/svg+xml';
@endphp

<div class="admin-image-uploader" id="uploader_{{ $inputId }}" data-input-id="{{ $inputId }}"
    data-modal-id="{{ $modalId }}" data-shape="{{ $shape }}">

    {{-- Real Hidden File Input Submitted With The Form --}}
    <input type="file" id="{{ $inputId }}" name="{{ $name }}" class="d-none actual-image-input" accept="{{ $accept }}"
        @if(!empty($required) && !$hasImage) required @endif>

    {{-- Hidden removal flag for edit forms --}}
    <input type="hidden" id="{{ $inputId }}_remove" name="remove_{{ $name }}" value="0" class="remove-image-flag">

    {{-- 1. EMPTY STATE: MODERN UPLOAD TRIGGER --}}
    <div class="image-empty-state {{ $hasImage ? 'd-none' : '' }}">
        <button type="button" class="btn-upload-trigger w-100 mb-0" data-bs-toggle="modal"
            data-bs-target="#{{ $modalId }}">
            <div class="upload-trigger-icon">
                <i class="{{ $icon ?? 'ri-upload-cloud-2-line' }}"></i>
            </div>
            <div class="upload-trigger-info">
                <span class="upload-trigger-title">{{ $label ?? 'Upload Image' }}</span>
                <span class="upload-trigger-sub">{{ $helpText ?? 'PNG, JPG, WebP up to ' . ($maxSizeMb ?? 10) . 'MB' }}</span>
            </div>
            <div class="upload-trigger-btn">
                <i class="ri-upload-2-line"></i>
                <span>Browse</span>
            </div>
        </button>
    </div>

    {{-- 2. PREVIEW STATE: Thumbnail, Name & Action Controls --}}
    <div class="image-preview-state {{ $hasImage ? '' : 'd-none' }}">
        <div class="position-relative mx-auto overflow-hidden border shadow-sm {{ $shape === 'circle' ? 'rounded-circle' : 'rounded' }}"
            style="height: {{ $height }}; width: {{ $width }}; background-color: #f8fafc;">
            <img src="{{ $currentImage ?? '' }}" alt="Image preview"
                class="image-preview-thumb w-100 h-100 object-fit-cover"
                onerror="this.onerror=null;this.src='{{ asset('admin/assets/images/default.jpg') }}';">

            <span style="position:absolute;top:8px;left:8px;z-index:3;font-size:11px;font-weight:600;padding:3px 8px;background:rgba(15,23,42,0.82);color:#fff;border-radius:4px;backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:4px;line-height:1.3;pointer-events:none;">
                <i class="ri-check-line" style="color:#22c55e;font-size:13px;"></i> Active
            </span>
        </div>

        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded border mt-2">
            <div class="text-start text-truncate me-2">
                <span class="image-preview-name fw-semibold text-dark d-block text-truncate" style="font-size: 12px;"
                    title="{{ $currentName ?? 'Uploaded image' }}">
                    {{ $currentName ?? 'Uploaded image' }}
                </span>
                <span class="image-preview-status text-muted" style="font-size: 11px;">Image selected</span>
            </div>
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-outline-secondary px-2" data-bs-toggle="modal"
                    data-bs-target="#{{ $modalId }}" style="font-size: 11.5px; height: 28px;" title="Change Image">
                    <i class="ri-refresh-line me-1"></i> Change
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger px-2 btn-remove-selected-image"
                    style="font-size: 11.5px; height: 28px;" title="Remove Image">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Error message placeholder --}}
    @error($name)
        <div class="text-danger mt-2" style="font-size: 12px;">{{ $message }}</div>
    @enderror

    {{-- UNIFIED MODAL COMPONENT --}}
    @include('admin.includes.upload-modal', [
        'modalId'        => $modalId,
        'modalTitle'     => $modalTitle ?? 'Upload Image',
        'helpText'       => $helpText ?? 'Select an image file and enter an asset name',
        'mode'           => 'single',
        'fileType'       => 'image',
        'accept'         => $accept,
        'maxSizeMb'      => $maxSizeMb ?? 10,
        'shape'          => $shape,
        'showNameInput'  => true,
        'nameLabel'      => $nameLabel ?? 'Image Name',
        'namePlaceholder'=> $namePlaceholder ?? 'e.g. Modern Architecture Facade',
        'currentName'    => $currentName ?? '',
        'applyBtnText'   => $applyBtnText ?? 'Apply Image',
        'icon'           => 'ri-upload-cloud-2-line',
        'instanceId'     => $inputId,
    ])
</div>