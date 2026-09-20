/**
 * Admin Panel Dedicated JavaScript
 * Fully decoupled from frontend scripts (zero dependencies on GSAP, ScrollTrigger, Lenis, or Swiper).
 */

function initAdmin() {
    // 1. Admin Sidebar Toggle Handler
    const sidebarToggleBtn = document.querySelector('#btn');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle('active');
        };
    }

    // Auto-scroll sidebar list to keep active menu item comfortably in view
    const activeSidebarLink = document.querySelector('.sidebar .active-focus');
    if (activeSidebarLink) {
        activeSidebarLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    // 2. User Profile Dropdown Toggle Handler (1 click open, 2nd click close immediately)
    const profileDropdownBtn = document.querySelector('#profileDropdownBtn');
    const profileDropdownMenu = document.querySelector('.main-header-dropdown');

    if (profileDropdownBtn && profileDropdownMenu) {
        // Toggle on profile button click
        profileDropdownBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Close Add New dropdown if open
            if (addNewDropdownMenu && addNewDropdownMenu.classList.contains('show')) {
                addNewDropdownMenu.classList.remove('show');
                if (addNewDropdownBtn) addNewDropdownBtn.setAttribute('aria-expanded', 'false');
            }

            const isOpen = profileDropdownMenu.classList.contains('show');
            if (isOpen) {
                profileDropdownMenu.classList.remove('show');
                profileDropdownBtn.setAttribute('aria-expanded', 'false');
            } else {
                profileDropdownMenu.classList.add('show');
                profileDropdownBtn.setAttribute('aria-expanded', 'true');
            }
        };

        // Close when clicking an item inside the dropdown
        profileDropdownMenu.querySelectorAll('.dropdown-item').forEach((item) => {
            item.onclick = () => {
                profileDropdownMenu.classList.remove('show');
                profileDropdownBtn.setAttribute('aria-expanded', 'false');
            };
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (profileDropdownMenu.classList.contains('show')) {
                if (!profileDropdownBtn.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                    profileDropdownMenu.classList.remove('show');
                    profileDropdownBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && profileDropdownMenu.classList.contains('show')) {
                profileDropdownMenu.classList.remove('show');
                profileDropdownBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 2b. Add New + Dropdown Toggle Handler
    const addNewDropdownBtn = document.querySelector('#headerAddNewDropdownBtn');
    const addNewDropdownMenu = document.querySelector('#headerAddNewDropdownMenu');

    if (addNewDropdownBtn && addNewDropdownMenu) {
        addNewDropdownBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Close profile dropdown if open
            if (profileDropdownMenu && profileDropdownMenu.classList.contains('show')) {
                profileDropdownMenu.classList.remove('show');
                if (profileDropdownBtn) profileDropdownBtn.setAttribute('aria-expanded', 'false');
            }

            const isOpen = addNewDropdownMenu.classList.contains('show');
            if (isOpen) {
                addNewDropdownMenu.classList.remove('show');
                addNewDropdownBtn.setAttribute('aria-expanded', 'false');
            } else {
                addNewDropdownMenu.classList.add('show');
                addNewDropdownBtn.setAttribute('aria-expanded', 'true');
            }
        };

        // Close when clicking an item inside
        addNewDropdownMenu.querySelectorAll('.dropdown-item').forEach((item) => {
            item.onclick = () => {
                addNewDropdownMenu.classList.remove('show');
                addNewDropdownBtn.setAttribute('aria-expanded', 'false');
            };
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (addNewDropdownMenu.classList.contains('show')) {
                if (!addNewDropdownBtn.contains(e.target) && !addNewDropdownMenu.contains(e.target)) {
                    addNewDropdownMenu.classList.remove('show');
                    addNewDropdownBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && addNewDropdownMenu.classList.contains('show')) {
                addNewDropdownMenu.classList.remove('show');
                addNewDropdownBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 3. Setup CSRF Token for jQuery AJAX if jQuery is loaded
    if (window.jQuery) {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfTokenMeta) {
            window.jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content')
                }
            });
        }

        // 4. Initialize Select2 on any element with .single-select2 if available
        if (window.jQuery.fn && window.jQuery.fn.select2) {
            const selectElements = window.jQuery('.single-select2');
            if (selectElements.length) {
                selectElements.select2();
            }
        }
    }

    // 5. Initialize Unified Admin Image Uploaders
    initAdminImageUploaders();
}

/**
 * Unified Admin Image Uploaders (Single button trigger + Modal with image name and dropzone)
 */
function initAdminImageUploaders() {
    document.querySelectorAll('.admin-image-uploader').forEach(uploader => {
        if (uploader.dataset.initialized === 'true') return;
        uploader.dataset.initialized = 'true';

        const inputId = uploader.dataset.inputId;
        const modalId = uploader.dataset.modalId;
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        const actualInput = uploader.querySelector('.actual-image-input');
        const removeFlag = uploader.querySelector('.remove-image-flag');
        const emptyState = uploader.querySelector('.image-empty-state');
        const previewState = uploader.querySelector('.image-preview-state');
        const previewThumb = uploader.querySelector('.image-preview-thumb');
        const previewName = uploader.querySelector('.image-preview-name');
        const removeBtn = uploader.querySelector('.btn-remove-selected-image');

        // Modal components
        const nameInput = modalEl.querySelector('.modal-image-name-input');
        const modalFileInput = modalEl.querySelector('.modal-file-picker');
        const dropzone = modalEl.querySelector('.modal-dropzone');
        const dropzoneEmpty = modalEl.querySelector('.modal-dropzone-empty');
        const dropzoneStaged = modalEl.querySelector('.modal-dropzone-staged');
        const stagedImg = modalEl.querySelector('.modal-staged-img');
        const stagedName = modalEl.querySelector('.modal-staged-name');
        const stagedSize = modalEl.querySelector('.modal-staged-size');
        const browseBtn = modalEl.querySelector('.btn-modal-browse');
        const reselectBtn = modalEl.querySelector('.btn-modal-reselect');
        const clearBtn = modalEl.querySelector('.btn-single-modal-clear');
        const applyBtn = modalEl.querySelector('.btn-modal-apply');

        let stagedFile = null;

        function formatBytes(bytes, decimals = 1) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function handleFileSelection(file) {
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                if (window.toastr) {
                    window.toastr.error('Please select a valid image file (JPG, PNG, WebP, SVG).', 'Invalid File');
                } else {
                    alert('Please select a valid image file.');
                }
                return;
            }

            stagedFile = file;
            const cleanName = file.name.replace(/\.[^/.]+$/, '').replace(/[_\-]+/g, ' ');

            // If name input empty or default, pre-fill with file's name capitalized
            if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                nameInput.value = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
            }

            // Preview in modal
            const reader = new FileReader();
            reader.onload = function(e) {
                if (stagedImg) stagedImg.src = e.target.result;
                if (stagedName) stagedName.textContent = file.name;
                if (stagedSize) stagedSize.textContent = formatBytes(file.size);

                if (dropzone) dropzone.classList.add('d-none');
                if (dropzoneStaged) dropzoneStaged.classList.remove('d-none');
                if (applyBtn) applyBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        // Clear/Remove button inside modal preview card
        if (clearBtn) {
            clearBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                stagedFile = null;
                if (modalFileInput) modalFileInput.value = '';
                if (dropzone) dropzone.classList.remove('d-none');
                if (dropzoneStaged) dropzoneStaged.classList.add('d-none');
                if (applyBtn) applyBtn.disabled = true;
            };
        }

        // Browse button
        if (browseBtn) {
            browseBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (modalFileInput) modalFileInput.click();
            };
        }

        // Reselect button
        if (reselectBtn) {
            reselectBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (modalFileInput) modalFileInput.click();
            };
        }

        // Dropzone click & drag drop
        if (dropzone) {
            dropzone.onclick = (e) => {
                if (e.target.closest('.btn-modal-reselect')) return;
                if (dropzoneEmpty && !dropzoneEmpty.classList.contains('d-none')) {
                    if (modalFileInput) modalFileInput.click();
                }
            };

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt ? dt.files : null;
                if (files && files.length > 0) {
                    handleFileSelection(files[0]);
                }
            });
        }

        // File picker change
        if (modalFileInput) {
            modalFileInput.onchange = () => {
                if (modalFileInput.files && modalFileInput.files.length > 0) {
                    handleFileSelection(modalFileInput.files[0]);
                }
            };
        }

        // Apply Image button in modal
        if (applyBtn) {
            applyBtn.onclick = (e) => {
                e.preventDefault();
                if (!stagedFile) return;

                const customName = nameInput ? nameInput.value.trim() : '';
                let fileToAssign = stagedFile;

                if (customName) {
                    const ext = stagedFile.name.split('.').pop();
                    const sanitized = customName.replace(/[^a-zA-Z0-9\-_ ]/g, '').trim().replace(/\s+/g, '-');
                    const newFileName = `${sanitized || 'image'}.${ext}`;
                    try {
                        fileToAssign = new File([stagedFile], newFileName, { type: stagedFile.type });
                    } catch (err) {
                        fileToAssign = stagedFile;
                    }
                }

                // Transfer file to form actual input
                try {
                    const dt = new DataTransfer();
                    dt.items.add(fileToAssign);
                    actualInput.files = dt.files;
                } catch (err) {
                    console.warn('DataTransfer not supported:', err);
                }

                // Trigger change event
                if (window.jQuery) {
                    window.jQuery(actualInput).trigger('change');
                } else {
                    actualInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                // Update preview in form card
                if (previewThumb) {
                    previewThumb.src = URL.createObjectURL(fileToAssign);
                }
                if (previewName) {
                    previewName.textContent = customName || fileToAssign.name;
                    previewName.title = customName || fileToAssign.name;
                }

                if (removeFlag) {
                    removeFlag.value = '0';
                }

                // Toggle visibility: hide empty state, show preview state
                if (emptyState) emptyState.classList.add('d-none');
                if (previewState) previewState.classList.remove('d-none');

                // Hide modal
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.hide();
                } else if (window.jQuery) {
                    window.jQuery(modalEl).modal('hide');
                }
            };
        }

        // Remove button on page preview
        if (removeBtn) {
            removeBtn.onclick = (e) => {
                e.preventDefault();
                try {
                    actualInput.value = '';
                    if (actualInput.files) {
                        actualInput.files = new DataTransfer().files;
                    }
                } catch(err) {}

                if (window.jQuery) {
                    window.jQuery(actualInput).trigger('change');
                } else {
                    actualInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (removeFlag) {
                    removeFlag.value = '1';
                }

                // Reset modal state
                stagedFile = null;
                if (modalFileInput) modalFileInput.value = '';
                if (dropzone) dropzone.classList.remove('d-none');
                if (dropzoneStaged) dropzoneStaged.classList.add('d-none');
                if (applyBtn) applyBtn.disabled = true;

                // Toggle visibility: hide preview, show empty state (button only)
                if (previewState) previewState.classList.add('d-none');
                if (emptyState) emptyState.classList.remove('d-none');
            };
        }

        // Reset modal state on close if not applied
        modalEl.addEventListener('hidden.bs.modal', function() {
            if (!actualInput.files || actualInput.files.length === 0) {
                if (dropzone) dropzone.classList.remove('d-none');
                if (dropzoneStaged) dropzoneStaged.classList.add('d-none');
                if (applyBtn) applyBtn.disabled = true;
                stagedFile = null;
                if (modalFileInput) modalFileInput.value = '';
            }
        });
    });
}

window.initAdminImageUploaders = initAdminImageUploaders;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdmin);
} else {
    initAdmin();
}
