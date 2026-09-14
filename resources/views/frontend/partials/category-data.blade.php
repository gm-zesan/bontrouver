@php
if (!isset($categoryData)) {
    $categoryData = \App\Services\CategoryService::getAll();
}
@endphp

