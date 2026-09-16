@props([
    'id',
    'title',
    'action' => null,
    'method' => 'POST',
    'buttonText' => 'Confirm',
    'buttonClass' => 'btn-danger',
    'buttonId' => null,
    'onClick' => null,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title text-white" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-white-50 text-wrap text-start">
                {{ $slot }}
            </div>
            <div class="modal-footer border-secondary border-opacity-25">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                @if($action)
                    <form action="{{ $action }}" method="POST" class="d-inline">
                        @csrf
                        @if(strtoupper($method) !== 'POST')
                            @method($method)
                        @endif
                        <button type="submit" class="btn {{ $buttonClass }} rounded-pill px-4" {!! $buttonId ? 'id="'.$buttonId.'"' : '' !!}>{{ $buttonText }}</button>
                    </form>
                @else
                    <button type="button" class="btn {{ $buttonClass }} rounded-pill px-4" {!! $buttonId ? 'id="'.$buttonId.'"' : '' !!} {!! $onClick ? 'onclick="'.$onClick.'"' : '' !!}>{{ $buttonText }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
