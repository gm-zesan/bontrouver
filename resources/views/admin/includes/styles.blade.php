<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<!-- Icon Library remixicon icon -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<!-- Fontawesome Icon CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Select2 css -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 40px !important;
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #49D17D !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 13.5px !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        line-height: 38px !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 40px !important;
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 4px 8px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #fff3ee !important;
        border: 1px solid rgba(249, 87, 22, 0.3) !important;
        border-radius: 4px !important;
        padding: 2px 8px !important;
        margin-top: 3px !important;
        margin-right: 6px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        font-size: 12.5px !important;
        color: #49D17D !important;
        font-weight: 600 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #49D17D !important;
        margin-right: 4px !important;
    }

    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        box-shadow: 0 10px 25px -5px rgba(11, 15, 23, 0.1) !important;
        overflow: hidden !important;
    }

    .select2-results__option--selectable {
        font-size: 13px !important;
        color: #1e293b !important;
        font-weight: 500 !important;
        padding: 8px 12px !important;
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #49D17D !important;
        color: #ffffff !important;
    }

    .select2-results__options::-webkit-scrollbar {
        width: 4px;
        background-color: #f1f5f9 !important;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
</style>

<!-- DatePicker plugin -->
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
    #datepicker {
        font-size: 13px;
        line-height: 18px;
    }

    .gj-icon {
        font-size: 18px !important;
        top: 8px !important;
        right: 8px !important;
    }
</style>

<!-- Toastr CSS & Modern Admin Styling -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Modern Enterprise Toastr Styling */
    #toast-container {
        position: fixed !important;
        z-index: 99999999 !important;
        pointer-events: auto;
    }

    #toast-container.toast-top-right,
    .toast-top-right {
        top: 10px !important;
        right: 30px !important;
    }

    #toast-container>div {
        opacity: 1 !important;
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 12px 36px -4px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.05) !important;
        border-radius: 12px !important;
        padding: 15px 20px 15px 54px !important;
        width: 360px !important;
        max-width: calc(100vw - 32px) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        position: relative !important;
        overflow: hidden !important;
        margin-bottom: 12px !important;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease !important;
    }

    #toast-container>div:hover {
        box-shadow: 0 18px 42px -4px rgba(15, 23, 42, 0.16), 0 6px 16px -2px rgba(15, 23, 42, 0.08) !important;
        transform: translateY(-2px) !important;
    }

    #toast-container>.toast-success {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310b981'%3E%3Cpath fill-rule='evenodd' d='M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-size: 24px 24px !important;
        background-position: 16px 16px !important;
        background-repeat: no-repeat !important;
    }

    #toast-container>.toast-error {
        border-left: 4.5px solid #ef4444 !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ef4444'%3E%3Cpath fill-rule='evenodd' d='M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-size: 24px 24px !important;
        background-position: 16px 16px !important;
        background-repeat: no-repeat !important;
    }

    #toast-container>.toast-warning {
        border-left: 4.5px solid #49D17D !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23f95716'%3E%3Cpath fill-rule='evenodd' d='M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-size: 24px 24px !important;
        background-position: 16px 16px !important;
        background-repeat: no-repeat !important;
    }

    #toast-container>.toast-info {
        border-left: 4.5px solid #3b82f6 !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233b82f6'%3E%3Cpath fill-rule='evenodd' d='M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-size: 24px 24px !important;
        background-position: 16px 16px !important;
        background-repeat: no-repeat !important;
    }

    .toast-title {
        font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em !important;
        margin-bottom: 2px !important;
        line-height: 1.35 !important;
    }

    .toast-message {
        font-family: 'Inter', -apple-system, sans-serif !important;
        font-size: 12.5px !important;
        font-weight: 500 !important;
        line-height: 1.45 !important;
        color: #475569 !important;
        word-break: break-word !important;
    }

    .toast-close-button {
        top: 10px !important;
        right: 12px !important;
        width: 22px !important;
        height: 22px !important;
        font-size: 16px !important;
        line-height: 20px !important;
        font-weight: 400 !important;
        color: #94a3b8 !important;
        opacity: 0.8 !important;
        border-radius: 6px !important;
        text-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        transition: all 0.15s ease !important;
    }

    .toast-close-button:hover {
        background-color: #f1f5f9 !important;
        opacity: 1 !important;
        color: #0f172a !important;
    }

    #toast-container>div .toast-progress {
        height: 3px !important;
        bottom: 0 !important;
        left: 0 !important;
        opacity: 0.9 !important;
        border-radius: 0 0 12px 12px !important;
    }

    #toast-container>.toast-success .toast-progress {
        background: linear-gradient(90deg, #10b981, #059669) !important;
    }

    #toast-container>.toast-error .toast-progress {
        background: linear-gradient(90deg, #ef4444, #dc2626) !important;
    }

    #toast-container>.toast-warning .toast-progress {
        background: linear-gradient(90deg, #49D17D, #35BE68) !important;
    }

    #toast-container>.toast-info .toast-progress {
        background: linear-gradient(90deg, #3b82f6, #2563eb) !important;
    }

    /* Universal Focus Reset: remove button & input focus outline and box-shadow */
    button:focus,
    button:focus-visible,
    button:active,
    input:focus,
    input:focus-visible,
    input:active,
    textarea:focus,
    textarea:focus-visible,
    textarea:active,
    select:focus,
    select:focus-visible,
    select:active,
    .btn:focus,
    .btn:focus-visible,
    .btn:active,
    .form-control:focus,
    .form-control:focus-visible,
    .form-control:active,
    .form-select:focus,
    .form-select:focus-visible,
    .form-select:active,
    .custom-input:focus,
    .custom-input:focus-visible,
    .custom-input:active,
    .submit-button:focus,
    .submit-button:focus-visible,
    .leave-button:focus,
    .leave-button:focus-visible,
    .add-new:focus,
    .add-new:focus-visible,
    .assign-role-btn:focus,
    .assign-role-btn:focus-visible,
    .paginate_button:focus,
    .paginate_button:focus-visible {
        outline: none !important;
        box-shadow: none !important;
    }
</style>

<!-- Admin SCSS Styles -->
@vite(['resources/scss/admin/style.scss', 'resources/scss/admin/table.scss'])