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

<style>
    .glass-modal {
        background-color: rgba(10, 15, 30, 0.1) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .glass-modal-content {
        background: linear-gradient(145deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95)) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border-radius: 16px !important;
    }

    .glass-modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .glass-modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        background: rgba(0, 0, 0, 0.2);
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .glass-modal-title {
        font-weight: 600;
        letter-spacing: -0.02em;
    }
</style>

<div class="modal fade glass-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal-content">
            <div class="modal-header glass-modal-header p-4">
                <h5 class="modal-title text-white glass-modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-white-50 text-wrap text-start p-4" style="font-size: 15px; line-height: 1.6;">
                {{ $slot }}
            </div>
            <div class="modal-footer glass-modal-footer p-4">
                <button type="button" class="btn btn-outline-light rounded-pill px-4"
                    style="border-color: rgba(255,255,255,0.2);" data-bs-dismiss="modal">Cancel</button>
                @if($action)
                    <form action="{{ $action }}" method="POST" class="d-inline">
                        @csrf
                        @if(strtoupper($method) !== 'POST')
                            @method($method)
                        @endif
                        <button type="submit" class="btn {{ $buttonClass }} rounded-pill px-4" {!! $buttonId ? 'id="' . $buttonId . '"' : '' !!}>{{ $buttonText }}</button>
                    </form>
                @else
                    <button type="button" class="btn {{ $buttonClass }} rounded-pill px-4" {!! $buttonId ? 'id="' . $buttonId . '"' : '' !!} {!! $onClick ? 'onclick="' . $onClick . '"' : '' !!}>{{ $buttonText }}</button>
                @endif
            </div>
        </div>
    </div>
</div>