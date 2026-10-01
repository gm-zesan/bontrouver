@props(['category'])

@php
    $slug = $category['slug'] ?? '';
    $url = $category['url'] ?? ($slug ? url('/category/' . $slug) : '#');
    $name = $category['name'] ?? '';
    $description = $category['description'] ?? '';
    $icon = $category['icon'] ?? 'bi-grid';
@endphp

<a href="{{ $url }}" class="category-card" id="cat-{{ $slug }}" aria-label="{{ $name }} - {{ $description }}">
    <div class="category-card-header">
        <div class="category-icon-box">
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
        </div>
        <div class="category-card-body">
            <h3 class="category-title">{{ $name }}</h3>
            <p class="category-description">{{ $description }}</p>
        </div>
    </div>
</a>