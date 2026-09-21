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
                <button type="button" class="btn btn-light btn-sm px-4 fw-semibold shadow-sm" data-bs-dismiss="modal" style="border-radius: 6px;">
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
        // Global Confirmation Modal Logic
        $(document).on('click', '.btn-confirm-modal', function (e) {
            e.preventDefault();
            
            var action = $(this).data('action');
            var method = $(this).data('method');
            var title = $(this).data('title');
            var desc = $(this).data('desc');
            var btnClass = $(this).data('btn-class');
            var btnText = $(this).data('btn-text');

            $('#confirmModalTitle').text(title);
            $('#confirmModalDesc').text(desc);
            $('#confirmModalForm').attr('action', action);
            $('#confirmModalMethod').val(method);
            
            // Set header styling based on button class (danger vs warning)
            if (btnClass.includes('danger')) {
                $('#confirmModalHeader').css({'background-color': '#fef2f2', 'border-bottom': '1px solid #fee2e2'});
                $('#confirmModalIcon').attr('class', 'ri-error-warning-fill me-2 text-danger');
                $('#confirmModalTitle').addClass('text-danger').removeClass('text-warning text-success text-primary');
            } else if (btnClass.includes('warning')) {
                $('#confirmModalHeader').css({'background-color': '#fffbeb', 'border-bottom': '1px solid #fef3c7'});
                $('#confirmModalIcon').attr('class', 'ri-error-warning-fill me-2 text-warning');
                $('#confirmModalTitle').addClass('text-warning').removeClass('text-danger text-success text-primary');
            } else {
                $('#confirmModalHeader').css({'background-color': '#f0fdf4', 'border-bottom': '1px solid #dcfce7'});
                $('#confirmModalIcon').attr('class', 'ri-information-fill me-2 text-success');
                $('#confirmModalTitle').addClass('text-success').removeClass('text-danger text-warning text-primary');
            }

            $('#confirmModalBtn').attr('class', 'btn btn-sm px-4 shadow-sm ' + btnClass).text(btnText);
            
            $('#confirmationModal').modal('show');
        });
    });
</script>
@endpush
