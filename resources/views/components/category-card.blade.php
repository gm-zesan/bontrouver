@props(['category'])

@php
    $slug = $category['slug'] ?? '';
    $url = $category['url'] ?? ($slug ? url('/' . $slug) : '#');
    $name = $category['name'] ?? '';
    $description = $category['description'] ?? '';
    $icon = $category['icon'] ?? 'bi-grid';
@endphp

<a href="{{ $url }}" class="category-card" id="cat-{{ $slug }}" onclick="openCategoryDrawer('{{ $slug }}'); return false;" aria-label="{{ $name }} - {{ $description }}">
    <div class="category-card-header">
        <div class="category-icon-box">
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
        </div>
        <span class="category-card-arrow" aria-hidden="true">
            <i class="bi bi-arrow-right"></i>
        </span>
    </div>
    
    <div class="category-card-body">
        <h3 class="category-title">{{ $name }}</h3>
        <p class="category-description">{{ $description }}</p>
    </div>
</a>
