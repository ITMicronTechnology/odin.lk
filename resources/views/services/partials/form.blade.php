{{-- Quote request form. Pass $fixedService to lock it to one service, or $categories to show a service picker. --}}
<form method="POST" action="{{ route('services.request.store') }}" class="row g-3" novalidate>
    @csrf
    <div class="visually-hidden" aria-hidden="true">
        <label for="website">Leave this field empty</label>
        <input id="website" name="website" tabindex="-1" autocomplete="off">
    </div>

    @isset($fixedService)
        <input type="hidden" name="service_id" value="{{ $fixedService->id }}">
    @else
        <div class="col-12">
            <label class="form-label" for="service_id">Which service do you need?</label>
            <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                <option value="">Not sure yet &mdash; help me choose</option>
                @foreach ($categories as $category)
                    <optgroup label="{{ $category->name }}">
                        @foreach ($category->services as $option)
                            <option value="{{ $option->id }}" @selected((int) old('service_id', $selected ?? null) === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endisset

    <div class="{{ $compact ?? false ? 'col-12' : 'col-md-6' }}">
        <label class="form-label" for="inq_name">Your name *</label>
        <input id="inq_name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user?->name) }}" required autocomplete="name">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="{{ $compact ?? false ? 'col-12' : 'col-md-6' }}">
        <label class="form-label" for="inq_company">Business / company</label>
        <input id="inq_company" name="company" class="form-control @error('company') is-invalid @enderror" value="{{ old('company') }}" autocomplete="organization">
        @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="{{ $compact ?? false ? 'col-12' : 'col-md-6' }}">
        <label class="form-label" for="inq_phone">Mobile number *</label>
        <input id="inq_phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user?->phone) }}" placeholder="077 123 4567" required autocomplete="tel">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="{{ $compact ?? false ? 'col-12' : 'col-md-6' }}">
        <label class="form-label" for="inq_email">Email *</label>
        <input id="inq_email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user?->email) }}" required autocomplete="email">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="{{ $compact ?? false ? 'col-6' : 'col-md-6' }}">
        <label class="form-label" for="inq_budget">Budget</label>
        <select id="inq_budget" name="budget" class="form-select @error('budget') is-invalid @enderror">
            <option value="">Select</option>
            @foreach (\App\Models\ServiceInquiry::BUDGETS as $budget)
                <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
            @endforeach
        </select>
    </div>
    <div class="{{ $compact ?? false ? 'col-6' : 'col-md-6' }}">
        <label class="form-label" for="inq_timeline">When do you need it?</label>
        <select id="inq_timeline" name="timeline" class="form-select @error('timeline') is-invalid @enderror">
            <option value="">Select</option>
            @foreach (\App\Models\ServiceInquiry::TIMELINES as $timeline)
                <option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="inq_message">Tell us about your project *</label>
        <textarea id="inq_message" name="message" rows="{{ $compact ?? false ? 4 : 5 }}" class="form-control @error('message') is-invalid @enderror"
                  placeholder="e.g. We are a hardware shop in Kandy and need a website with our products and a WhatsApp order button." required>{{ old('message') }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <button class="btn btn-odin btn-lg w-100"><i class="bi bi-send me-1"></i> Request a Free Quote</button>
        <p class="small text-muted text-center mt-2 mb-0"><i class="bi bi-shield-check me-1"></i>No obligation &middot; we reply within 1 working day</p>
    </div>
</form>
