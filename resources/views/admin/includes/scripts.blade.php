<script>
    window.SITEURL = "{{ url('/') }}";
</script>

<!-- Jquery CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Bootstrap 5 bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    if (typeof $.fn.dataTable !== 'undefined') {
        $.extend(true, $.fn.dataTable.defaults, {
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'table-responsive't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            autoWidth: false,
            responsive: false,
            language: {
                search: "",
                searchPlaceholder: "Search...",
                paginate: {
                    first: '<i class="ri-skip-back-mini-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    last: '<i class="ri-skip-forward-mini-line"></i>'
                }
            }
        });
    }
</script>

<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        if ($('.single-select2').length) {
            $('.single-select2').select2();
        }
    });
</script>

<!-- DatePicker plugin -->
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

<!-- Common Scripts -->
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    });
</script>

<!-- Toastr JS & Global Flash Message Handler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "250",
        "hideDuration": "300",
        "timeOut": "1500",
        "extendedTimeOut": "1200",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    window.showToast = function (message, isError = false, title = '') {
        if (isError) {
            toastr.error(message, title || 'Error');
        } else {
            toastr.success(message, title || 'Success');
        }
    };

    window.notify = function (type, message, title = '') {
        if (type === 'error' || type === 'danger') {
            toastr.error(message, title || 'Error');
        } else if (type === 'warning') {
            toastr.warning(message, title || 'Warning');
        } else if (type === 'info') {
            toastr.info(message, title || 'Notice');
        } else {
            toastr.success(message, title || 'Success');
        }
    };

    // Trigger Laravel Session Flash Messages
    $(document).ready(function () {
        @if(Session::has('success'))
            toastr.success("{!! addslashes(Session::get('success')) !!}", "Success");
        @endif

        @if(Session::has('message'))
            toastr.success("{!! addslashes(Session::get('message')) !!}", "Success");
        @endif

        @if(Session::has('error'))
            toastr.error("{!! addslashes(Session::get('error')) !!}", "Error");
        @endif

        @if(Session::has('warning'))
            toastr.warning("{!! addslashes(Session::get('warning')) !!}", "Warning");
        @endif

        @if(Session::has('info'))
            toastr.info("{!! addslashes(Session::get('info')) !!}", "Notice");
        @endif

        @if(Session::has('status'))
            toastr.info("{!! addslashes(Session::get('status')) !!}", "Status");
        @endif

        @if(isset($errors) && $errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{!! addslashes($error) !!}", "Validation Error");
            @endforeach
        @endif
    });
</script>

<!-- Dedicated Admin Panel JavaScript -->
@vite(['resources/js/admin.js'])