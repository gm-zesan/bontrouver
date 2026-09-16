@extends('frontend.account.layout', ['pageTitle' => 'Edit Meetup', 'activeNav' => 'meetups'])

@section('account_content')
    <style>
        .create-meetup-card {
            background: #0D243C;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
        }

        .create-meetup-card .form-label {
            color: #FFFFFF;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .create-meetup-card .form-control,
        .create-meetup-card .form-select {
            background-color: #06182B !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #FFFFFF !important;
            border-radius: 10px;
            padding: 0.65rem 1rem;
        }

        .create-meetup-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4) !important;
        }

        .create-meetup-card .form-control:focus,
        .create-meetup-card .form-select:focus {
            background-color: #06182B !important;
            border-color: #49D17D !important;
            color: #FFFFFF !important;
            box-shadow: 0 0 0 0.25rem rgba(73, 209, 125, 0.2) !important;
        }

        .create-meetup-card .form-text {
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .expense-radio-card {
            background: #06182B !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .expense-radio-card:hover {
            border-color: rgba(73, 209, 125, 0.4) !important;
            background: #092036 !important;
        }

        .expense-radio-card:has(input:checked) {
            border-color: #49D17D !important;
            background: rgba(73, 209, 125, 0.1) !important;
            box-shadow: 0 0 12px rgba(73, 209, 125, 0.15);
        }

        .text-success-custom {
            color: #34D399 !important;
        }

        .text-primary-custom {
            color: #60A5FA !important;
        }

        .text-warning-custom {
            color: #FBBF24 !important;
        }
    </style>

    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-white d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-primary"></i>
                Edit Meetup
            </h1>
            <p class="text-white-50 small mb-0">Create a safe and friendly community event in your neighborhood.</p>
        </div>
        <a href="{{ route('meetups.my') }}" class="btn btn-outline-light rounded-pill px-3 py-2 small">
            <i class="bi bi-arrow-left me-1"></i>
            <span>Back to My Meetups</span>
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="create-meetup-card shadow-lg">
        <form action="{{ route('community.update', $meetup->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Column: Details -->
                <div class="col-md-6">
                    <h4 class="fs-5 fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        Meetup Details
                    </h4>

                    <div class="mb-3">
                        <label class="form-label">Activity Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="" disabled selected>Select an activity type...</option>
                            @foreach(\App\Enums\CompanionshipType::values() as $type)
                                <option value="{{ $type }}" {{ old('type', $meetup->type) == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meetup Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $meetup->title) }}"
                            placeholder="e.g. Saturday Morning Hike at Mount Royal" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="meetup_date_time"
                            class="form-control @error('meetup_date_time') is-invalid @enderror"
                            value="{{ old('meetup_date_time', $meetup->meetup_date_time->format('Y-m-d\TH:i')) }}" required>
                        @error('meetup_date_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="5" placeholder="Describe the meetup, what to expect, and who should join..."
                            required>{{ old('description', $meetup->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column: Logistics -->
                <div class="col-md-6">
                    <h4 class="fs-5 fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt"></i>
                        Logistics & Location
                    </h4>

                    <div class="mb-3">
                        <label class="form-label">Location / Venue Name <span class="text-danger">*</span></label>
                        <input type="text" name="location_name"
                            class="form-control @error('location_name') is-invalid @enderror"
                            value="{{ old('location_name', $meetup->location_name) }}"
                            placeholder="e.g. Tim Hortons, High Park Entrance" required>
                        @error('location_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-8">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', $meetup->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Prov. <span class="text-danger">*</span></label>
                            <select name="province" class="form-select @error('province') is-invalid @enderror" required>
                                @foreach(['AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'NT', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT'] as $prov)
                                    <option value="{{ $prov }}" {{ old('province', $meetup->province) == $prov ? 'selected' : '' }}>
                                        {{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Headcount Limit (Optional)</label>
                        <input type="number" name="headcount_limit"
                            class="form-control @error('headcount_limit') is-invalid @enderror"
                            value="{{ old('headcount_limit', $meetup->headcount_limit) }}"
                            placeholder="Leave blank for unlimited" min="2" max="100">
                        <div class="form-text">Maximum number of people who can join.</div>
                        @error('headcount_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block mb-3">Expense Handling <span class="text-danger">*</span></label>

                        <!-- Option 1: Free -->
                        <div class="card mb-2 expense-radio-card">
                            <div class="card-body py-2 px-3">
                                <div class="form-check d-flex align-items-center mb-0">
                                    <input class="form-check-input flex-shrink-0 me-3" type="radio" name="expense_type"
                                        id="expenseFree" value="free" {{ old('expense_type', $meetup->expense_type) == 'free' ? 'checked' : '' }}>
                                    <label class="form-check-label flex-grow-1 cursor-pointer w-100 m-0 py-1"
                                        for="expenseFree">
                                        <div class="fw-bold text-success-custom"><i class="bi bi-balloon me-1"></i> Free
                                            Activity</div>
                                        <div class="small text-white-50">No costs involved (e.g. walking in the park)</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: Split -->
                        <div class="card mb-2 expense-radio-card">
                            <div class="card-body py-2 px-3">
                                <div class="form-check d-flex align-items-center mb-0">
                                    <input class="form-check-input flex-shrink-0 me-3" type="radio" name="expense_type"
                                        id="expenseSplit" value="split" {{ old('expense_type', $meetup->expense_type) == 'split' ? 'checked' : '' }}>
                                    <label class="form-check-label flex-grow-1 cursor-pointer w-100 m-0 py-1"
                                        for="expenseSplit">
                                        <div class="fw-bold text-primary-custom"><i class="bi bi-pie-chart me-1"></i> Split
                                            the Bill</div>
                                        <div class="small text-white-50">Everyone pays for themselves (e.g. coffee shop)
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Option 3: Host Pays -->
                        <div class="card expense-radio-card">
                            <div class="card-body py-2 px-3">
                                <div class="form-check d-flex align-items-center mb-0">
                                    <input class="form-check-input flex-shrink-0 me-3" type="radio" name="expense_type"
                                        id="expenseHost" value="host_pays" {{ old('expense_type') == 'host_pays' ? 'checked' : '' }}>
                                    <label class="form-check-label flex-grow-1 cursor-pointer w-100 m-0 py-1"
                                        for="expenseHost">
                                        <div class="fw-bold text-warning-custom"><i class="bi bi-gift me-1"></i> Host Pays
                                        </div>
                                        <div class="small text-white-50">You are covering the cost for attendees</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('expense_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Form Footer Buttons -->
            <div
                class="pt-4 mt-2 d-flex justify-content-end align-items-center gap-3 border-top border-secondary border-opacity-10">
                <a href="{{ route('meetups.my') }}" class="btn btn-outline-light rounded-pill px-4 py-2">
                    <span>Cancel</span>
                </a>
                <button type="submit" class="hero-btn-primary"
                    style="min-width: auto; border: none; padding: 0.6rem 1.8rem;">
                    <i class="bi bi-check-lg me-1"></i>
                    <span>Update Meetup</span>
                </button>
            </div>
        </form>
    </div>
@endsection