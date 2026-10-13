@props([
    'listingId',
    'scope' => 'card',
    'targetLocale' => null,
    'btnClass' => '',
])

@php
    $id = is_object($listingId) ? ($listingId->id ?? '') : $listingId;
    $activeLocale = app()->getLocale();
    $target = $targetLocale ?? ($activeLocale === 'fr' ? 'fr' : 'en');
    $isTargetFrench = ($target === 'fr');
    $labelTranslate = $isTargetFrench ? __('Traduire') : __('Translate');
    $labelOriginal = $isTargetFrench ? __("Voir l'original") : __('Show Original');
@endphp

<button type="button"
        class="btn-listing-translate {{ $btnClass }}"
        data-action="translate-listing"
        data-listing-id="{{ $id }}"
        data-scope="{{ $scope }}"
        data-target-locale="{{ $target }}"
        data-label-translate="{{ $labelTranslate }}"
        data-label-original="{{ $labelOriginal }}"
        onclick="toggleListingTranslation(this, event)"
        aria-label="{{ $labelTranslate }}"
        title="{{ $labelTranslate }}">
    <i class="bi bi-translate translate-icon"></i>
    <span class="spinner-border spinner-border-sm translate-spinner d-none" role="status" aria-hidden="true"></span>
    <span class="translate-btn-label">{{ $labelTranslate }}</span>
</button>
