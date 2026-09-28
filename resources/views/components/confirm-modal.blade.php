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
        background-color: rgba(6, 18, 32, 0.75) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .glass-modal-content {
        background: linear-gradient(145deg, #0F2742 0%, #091D33 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border-radius: 16px !important;
        color: #E2E8F0;
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
        font-weight: 700;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        letter-spacing: -0.01em;
    }
</style>

<div class="modal fade glass-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal-content">
            <div class="modal-header glass-modal-header px-4 py-3">
                <h5 class="modal-title text-white glass-modal-title" id="{{ $id }}Label">{!! $title !!}</h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-white-50 text-wrap text-start p-4" style="font-size: 14.5px; line-height: 1.6;">
                {{ $slot }}
            </div>
            <div class="modal-footer glass-modal-footer px-4 py-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-semibold"
                    style="border-color: rgba(255,255,255,0.2); font-size: 0.88rem;" data-bs-dismiss="modal">Cancel</button>
                @if($action)
                    <form action="{{ $action }}" method="POST" class="d-inline" onsubmit="let b = this.querySelector('button[type=submit]'); b.style.pointerEvents = 'none'; b.style.opacity = '0.7'; b.innerHTML = '<span class=\'spinner-border spinner-border-sm me-1\'></span> Processing...';">
                        @csrf
                        @if(strtoupper($method) !== 'POST')
                            @method($method)
                        @endif
                        <button type="submit" class="btn {{ $buttonClass }} rounded-pill px-4 fw-semibold" style="font-size: 0.88rem;" {!! $buttonId ? 'id="' . $buttonId . '"' : '' !!}>{!! $buttonText !!}</button>
                    </form>
                @else
                    <button type="button" class="btn {{ $buttonClass }} rounded-pill px-4 fw-semibold" style="font-size: 0.88rem;" {!! $buttonId ? 'id="' . $buttonId . '"' : '' !!} {!! $onClick ? 'onclick="' . $onClick . '"' : '' !!}>{!! $buttonText !!}</button>
                @endif
            </div>
        </div>
    </div>
</div>