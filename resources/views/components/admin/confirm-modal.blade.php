<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header" id="confirmModalHeader" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="modal-title fw-bold d-flex align-items-center" style="font-size: 16px;">
                    <i id="confirmModalIcon" class="ri-error-warning-fill me-2 text-primary"></i> 
                    <span id="confirmModalTitle">Confirm Action</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p id="confirmModalDesc" class="mb-0 text-muted" style="font-size: 14.5px;">Are you sure you want to proceed?</p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: none; padding-bottom: 24px;">
                <button type="button" id="confirmModalDismissBtn" class="btn btn-light btn-sm px-4 fw-semibold shadow-sm" data-bs-dismiss="modal" style="border-radius: 6px;">
                    Cancel
                </button>
                <form id="confirmModalForm" action="#" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="_method" id="confirmModalMethod" value="POST">
                    <button type="submit" id="confirmModalBtn" class="btn btn-sm px-4 shadow-sm" style="font-weight: 500; border-radius: 6px;">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-script')
<script>
    $(document).ready(function() {
        var currentConfirmCallback = null;
        var currentResolvePromise = null;

        function applyModalTheme(btnClass, title) {
            btnClass = btnClass || 'btn-primary';
            if (btnClass.includes('danger')) {
                $('#confirmModalHeader').css({'background-color': '#fef2f2', 'border-bottom': '1px solid #fee2e2'});
                $('#confirmModalIcon').attr('class', 'ri-error-warning-fill me-2 text-danger');
                $('#confirmModalTitle').addClass('text-danger').removeClass('text-warning text-success text-primary');
            } else if (btnClass.includes('warning')) {
                $('#confirmModalHeader').css({'background-color': '#fffbeb', 'border-bottom': '1px solid #fef3c7'});
                $('#confirmModalIcon').attr('class', 'ri-alert-fill me-2 text-warning');
                $('#confirmModalTitle').addClass('text-warning').removeClass('text-danger text-success text-primary');
            } else if (btnClass.includes('success')) {
                $('#confirmModalHeader').css({'background-color': '#f0fdf4', 'border-bottom': '1px solid #dcfce7'});
                $('#confirmModalIcon').attr('class', 'ri-checkbox-circle-fill me-2 text-success');
                $('#confirmModalTitle').addClass('text-success').removeClass('text-danger text-warning text-primary');
            } else {
                $('#confirmModalHeader').css({'background-color': '#f0f9ff', 'border-bottom': '1px solid #e0f2fe'});
                $('#confirmModalIcon').attr('class', 'ri-information-fill me-2 text-primary');
                $('#confirmModalTitle').addClass('text-primary').removeClass('text-danger text-warning text-success');
            }
        }

        // Global Form Submission / Callback Handler
        $('#confirmModalForm').on('submit', function(e) {
            if (typeof currentConfirmCallback === 'function') {
                e.preventDefault();
                var cb = currentConfirmCallback;
                currentConfirmCallback = null;
                if (currentResolvePromise) {
                    currentResolvePromise({ isConfirmed: true });
                    currentResolvePromise = null;
                }
                $('#confirmationModal').modal('hide');
                cb();
                return false;
            }

            if (currentResolvePromise) {
                currentResolvePromise({ isConfirmed: true });
                currentResolvePromise = null;
            }
            // If it's a standard form action, allow standard form submission
        });

        $('#confirmationModal').on('hidden.bs.modal', function() {
            if (currentResolvePromise) {
                currentResolvePromise({ isConfirmed: false });
                currentResolvePromise = null;
            }
            currentConfirmCallback = null;
            $('#confirmModalForm').show().attr('action', '#');
            $('#confirmModalDismissBtn').text('Cancel').addClass('btn-light').removeClass('btn-warning text-white');
        });

        // Global Confirmation Modal Logic for data-attributes
        $(document).on('click', '.btn-confirm-modal', function (e) {
            e.preventDefault();
            currentConfirmCallback = null;
            currentResolvePromise = null;

            var action = $(this).data('action');
            var method = $(this).data('method') || 'POST';
            var title = $(this).data('title') || 'Confirm Action';
            var desc = $(this).data('desc') || 'Are you sure you want to proceed?';
            var btnClass = $(this).data('btn-class') || 'btn-primary';
            var btnText = $(this).data('btn-text') || 'Confirm';

            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalForm').show().attr('action', action);
            $('#confirmModalMethod').val(method);
            $('#confirmModalBtn').attr('class', 'btn btn-sm px-4 shadow-sm ' + btnClass).text(btnText);
            $('#confirmModalDismissBtn').text('Cancel').addClass('btn-light').removeClass('btn-warning text-white');

            applyModalTheme(btnClass, title);
            $('#confirmationModal').modal('show');
        });

        // Global Universal Confirm / Warning Modal
        window.showConfirmModal = function(options) {
            return new Promise(function(resolve) {
                currentResolvePromise = resolve;
                currentConfirmCallback = options.onConfirm || null;

                var title = options.title || 'Confirm Action';
                var message = options.message || options.desc || 'Are you sure you want to proceed?';
                var btnText = options.confirmButtonText || options.btnText || 'Confirm';
                var btnClass = options.confirmButtonClass || options.btnClass || 'btn-danger';
                var cancelText = options.cancelButtonText || 'Cancel';

                $('#confirmModalTitle').text(title);
                $('#confirmModalDesc').text(message);
                $('#confirmModalForm').show().attr('action', options.action || '#');
                $('#confirmModalMethod').val(options.method || 'POST');
                $('#confirmModalBtn').attr('class', 'btn btn-sm px-4 shadow-sm ' + btnClass).text(btnText);
                $('#confirmModalDismissBtn').text(cancelText).addClass('btn-light').removeClass('btn-warning text-white');

                applyModalTheme(btnClass, title);
                $('#confirmationModal').modal('show');
            });
        };

        // Global Warning Modal Helper (handles both object configs and direct string arguments)
        window.showWarningModal = function(titleOrOptions, message) {
            // Case 1: Config Object passed (e.g. { title, message, onConfirm, ... })
            if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
                // If it has a confirm callback or confirm button, delegate to showConfirmModal
                if (titleOrOptions.onConfirm || titleOrOptions.confirmButtonText || titleOrOptions.confirmButtonClass) {
                    return window.showConfirmModal(titleOrOptions);
                }

                return new Promise(function(resolve) {
                    currentResolvePromise = resolve;
                    currentConfirmCallback = null;

                    var title = titleOrOptions.title || 'Warning';
                    var desc = titleOrOptions.message || titleOrOptions.desc || 'Are you sure you want to proceed?';

                    $('#confirmModalTitle').text(title).addClass('text-warning').removeClass('text-danger text-success text-primary');
                    $('#confirmModalDesc').text(desc);
                    $('#confirmModalHeader').css({'background-color': '#fffbeb', 'border-bottom': '1px solid #fef3c7'});
                    $('#confirmModalIcon').attr('class', 'ri-alert-fill me-2 text-warning');
                    $('#confirmModalForm').hide();
                    $('#confirmModalDismissBtn').text('Understood').removeClass('btn-light').addClass('btn-warning text-white');

                    $('#confirmationModal').modal('show');
                });
            }

            // Case 2: Simple warning / alert strings: showWarningModal(title, message)
            return new Promise(function(resolve) {
                currentResolvePromise = resolve;
                currentConfirmCallback = null;

                var title = (typeof titleOrOptions === 'string' && titleOrOptions.trim() !== '') ? titleOrOptions : 'Warning';
                var desc = message || 'Are you sure you want to proceed?';

                // If only 1 string argument or message is omitted
                $('#confirmModalTitle').text(title).addClass('text-warning').removeClass('text-danger text-success text-primary');
                $('#confirmModalDesc').text(desc);
                $('#confirmModalHeader').css({'background-color': '#fffbeb', 'border-bottom': '1px solid #fef3c7'});
                $('#confirmModalIcon').attr('class', 'ri-alert-fill me-2 text-warning');
                $('#confirmModalForm').hide();
                $('#confirmModalDismissBtn').text('Understood').removeClass('btn-light').addClass('btn-warning text-white');

                $('#confirmationModal').modal('show');
            });
        };
    });
</script>
@endpush
