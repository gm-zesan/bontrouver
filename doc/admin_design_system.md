# Bon Trouver — Admin Panel UI & Design System Specification

This document serves as the **authoritative UI/UX and Component Specification** for all administrative modules in Bon Trouver. **Every admin module (Users, Listings, Categories, Verification, Reports, Roles, Settings) MUST follow these exact design rules, CSS classes, component structures, and interaction patterns.**

---

## 1. Design Philosophy & Golden Rules

1. **No Inline Style Clutter**: All typography, sizing, layout, paddings, and colors must come from compiled SCSS (`resources/scss/admin/style.scss` and `table.scss`).
2. **Standardized Control Heights (`34px`)**:
   - All text inputs, search fields, filter selects, and action buttons in table toolbars must be exactly **34px** in height (`height: 34px; line-height: 34px;`).
3. **Modal-First Confirmation**:
   - **Never** use native browser `alert()` or `confirm()`.
   - Always use `<x-admin.confirm-modal />` and `window.showWarningModal(title, message)`.
4. **Toast Feedback**:
   - Always trigger `window.showToast(message, isError, title)` on asynchronous actions (AJAX saves, bulk operations, status changes).
5. **No Static Mock Data**: All metrics, statuses, dropdown options, and badges must be 100% database-driven and Eloquent relationship-aware.

---

## 2. Standard Page Layouts

### A. List / Index Page Pattern (`index.blade.php`)

Every administrative list page follows this standard structure:

```blade
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid my-3 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                {{-- Card Header: Title & Breadcrumb on left, Filters & Bulk Actions on right --}}
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    {{-- 1. Title & Breadcrumbs --}}
                    <div class="title-with-breadcrumb">
                        <div class="fw-bold text-dark fs-6 mb-1">{Module Name}</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{Module}</li>
                            </ol>
                        </nav>
                    </div>

                    {{-- 2. Filters & Bulk Actions Toolbar --}}
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        {{-- Filters --}}
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter_status" class="form-select table-filter-select">
                                <option value="">All Statuses</option>
                                ...
                            </select>
                            <button type="button" id="btn_apply_filters" class="btn btn-light border table-filter-btn">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>

                        {{-- Bulk Actions (Reactive Disabled/Enabled State) --}}
                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                            <select id="bulk_action_type" class="form-select table-bulk-select" disabled>
                                <option value="">Bulk Actions...</option>
                                <option value="activate">Activate Selected</option>
                                <option value="suspend">Suspend Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" id="btn_apply_bulk" class="btn btn-dark table-bulk-btn" disabled>Apply</button>
                        </div>
                    </div>
                </div>

                {{-- Card Body: DataTables Container --}}
                <div class="card-body p-4">
                    <table class="table dataTable w-100 align-middle" id="data-table">
                        <thead>
                            <tr>
                                <th scope="col" class="th-checkbox">
                                    <div class="form-check m-0">
                                        <input class="form-check-input border-secondary" type="checkbox" id="check_all_items">
                                    </div>
                                </th>
                                <th scope="col" class="th-index">#</th>
                                ...
                                <th scope="col" class="th-action">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### B. Detail & Management Page Pattern (`show.blade.php`)

Every detailed overview page utilizes the **2-Column Layout**:
- **Left Column (`col-lg-4`)**:
  - **Summary Card**: Primary avatar/image, title, role/type pill, status buttons (Suspend/Delete with confirm modal), key metrics/tiers, and timestamps.
  - **Internal Admin Notes Box (`.admin-notes-card`)**: Private textarea with instant AJAX save + auto-saved badge + toast trigger.
- **Right Column (`col-lg-8`)**:
  - **Navigation Pills (`.custom-admin-tabs`)**: Sticky/smooth horizontal tab bar saving active tab to `localStorage` and URL hash.
  - **Tab Panes**: Form management tabs and dynamic data tables with AJAX pagination (no full page reload).

---

## 3. Standard SCSS Classes & CSS Variables

All styles reside in `resources/scss/admin/` and are compiled via Vite:

### Table Classes (`resources/scss/admin/table.scss`)
- `.table-filter-select`: Height `34px`, font size `12.5px`, border `#e2e8f0`, radius `6px`.
- `.table-filter-btn`: Height `34px`, font size `12.5px`, background `#f8fafc`.
- `.table-bulk-select`: Height `34px`, font size `12.5px`.
- `.table-bulk-btn`: Height `34px`, font size `12.5px`, radius `6px`.
- `.th-checkbox`: Width `40px`, padding `12px 16px`, center aligned.
- `.th-index`: Width `60px`, font weight `600`, color `#475569`.
- `.th-action`: Text align `end`, padding right `16px`.
- `.custom-admin-table`: Clean borders `#f1f5f9`, hover state `#f8fafc`, th color `#475569`.

### UI Component Classes (`resources/scss/admin/style.scss`)
- `.user-avatar-verified-badge`: 1:1 circle, `22px x 22px`, `#49D17D`, pure white checkmark, bottom right corner.
- `.tier-badge`: High-contrast ranking pill:
  - `.tier-bronze`: `#d97706` text on `#fffbeb` background, border `rgba(217, 119, 6, 0.25)`.
  - `.tier-silver`: `#475569` text on `#f1f5f9` background, border `rgba(71, 85, 105, 0.25)`.
  - `.tier-gold`: `#b45309` text on `#fef3c7` background, border `rgba(180, 83, 9, 0.3)`.
  - `.tier-platinum`: `#0284c7` text on `#e0f2fe` background, border `rgba(2, 132, 199, 0.3)`.
- `.admin-notes-card`: Dedicated private moderation card with green `#49D17D` lock icon, textarea, and AJAX trigger.
- `.custom-admin-tabs`: Active tab `#ffffff` with subtle elevation and `#0f172a` active text.

---

## 4. JavaScript Patterns & Standards

### 1. Reactive Bulk Action States
```javascript
function updateBulkActionState() {
    var selectedCount = $('.user-checkbox:checked').length;
    var actionSelected = $('#bulk_action_type').val() !== '';

    if (selectedCount > 0) {
        $('#bulk_action_type').prop('disabled', false);
        $('#btn_apply_bulk').prop('disabled', !actionSelected);
    } else {
        $('#bulk_action_type').val('').prop('disabled', true);
        $('#btn_apply_bulk').prop('disabled', true);
    }
}
```

### 2. Modal Confirmation Hookup
All buttons triggering destructive or significant state modifications must utilize:
```html
<button type="button" 
    class="btn btn-confirm-modal" 
    data-action="/target/route" 
    data-method="POST|DELETE" 
    data-title="Modal Title" 
    data-desc="Description text..." 
    data-btn-class="btn-danger|btn-warning|btn-success" 
    data-btn-text="Button Label">
    Action
</button>
```

### 3. Warning Modal Trigger
```javascript
window.showWarningModal("Selection Required", "Please select at least one item from the table.");
```

### 4. Toast Notifications Trigger
```javascript
window.showToast("Changes saved successfully.", false, "Success");
window.showToast("An unexpected error occurred.", true, "Error");
```
