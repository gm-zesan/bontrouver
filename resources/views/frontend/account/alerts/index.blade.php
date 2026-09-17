@extends('frontend.account.layout', ['activeNav' => 'alerts', 'pageTitle' => 'Smart Alerts'])

@section('account_content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-search-heart text-brand-green"></i>
                <span>Smart Alerts</span>
            </h1>
            <p class="text-secondary mb-0">Manage your automated notifications for new listings.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('account.alerts.create') }}" class="btn btn-theme-primary rounded-pill px-4 fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i>
                <span class="d-none d-md-inline">Create Alert</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i> 
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">
        @if($alerts->isEmpty())
            <div class="dark-surface-card p-5 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-secondary bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-bell-slash text-secondary fs-1"></i>
                </div>
                <h5 class="text-white mb-2">No Smart Alerts Yet</h5>
                <p class="text-secondary mb-4">Set up an alert to get notified when a listing matches your exact criteria.</p>
                <a href="{{ route('account.alerts.create') }}" class="btn btn-outline-light rounded-pill px-4">Create Your First Alert</a>
            </div>
        @else
            @foreach($alerts as $alert)
                <div class="dark-surface-card p-4 transition-hover border border-secondary border-opacity-10 position-relative">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="fw-bold text-white mb-0">{{ $alert->name }}</h5>
                                @if($alert->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 my-listing-badge px-2">Active</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25 my-listing-badge px-2">Inactive</span>
                                @endif
                            </div>
                            <div class="small text-secondary mb-3">
                                <i class="bi bi-clock me-1"></i> Created {{ $alert->created_at->format('M d, Y') }}
                            </div>
                            
                            <!-- Filters -->
                            <div class="d-flex flex-wrap gap-2">
                                @if($alert->category)
                                    <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-tag-fill text-secondary"></i> {{ $alert->category->name }}
                                    </span>
                                @endif
                                @if($alert->city)
                                    <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $alert->city }}
                                    </span>
                                @endif
                                @if($alert->min_price || $alert->max_price)
                                    <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-currency-dollar text-success"></i> 
                                        {{ $alert->min_price ? '$' . $alert->min_price : '$0' }} - {{ $alert->max_price ? '$' . $alert->max_price : 'Max' }}
                                    </span>
                                @endif
                                @if($alert->keyword)
                                    <span class="badge bg-dark-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-search text-info"></i> "{{ $alert->keyword }}"
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start gap-2 flex-shrink-0">
                            <form action="{{ route('account.alerts.destroy', $alert) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this alert?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark border border-secondary border-opacity-25 text-danger px-3 py-2 rounded-pill" title="Delete Alert">
                                    <i class="bi bi-trash-fill me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
