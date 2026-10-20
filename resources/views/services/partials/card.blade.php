<div class="svc-card">
    <div class="d-flex align-items-start gap-3 mb-2">
        <span class="svc-icon"><i class="bi {{ $service->icon ?: 'bi-gear' }}"></i></span>
        <div>
            <h3 class="svc-name"><a href="{{ route('services.show', $service) }}">{{ $service->name }}</a></h3>
            @if ($service->is_featured)<span class="badge bg-odin-red">Popular</span>@endif
        </div>
    </div>
    <p class="svc-desc">{{ $service->short_description }}</p>
    @if ($service->features)
        <ul class="svc-features">
            @foreach (array_slice($service->features, 0, 3) as $feature)
                <li><i class="bi bi-check2-circle"></i>{{ $feature }}</li>
            @endforeach
        </ul>
    @endif
    <div class="svc-foot">
        <div>
            @if ($service->starting_price)
                <div class="svc-price-label">Starting from</div>
                <div class="svc-price">@money($service->starting_price) <small>{{ $service->price_note }}</small></div>
            @else
                <div class="svc-price-label">Pricing</div>
                <div class="svc-price">Free quotation</div>
            @endif
        </div>
        @if ($service->delivery_time)
            <div class="svc-time"><i class="bi bi-clock"></i> {{ $service->delivery_time }}</div>
        @endif
    </div>
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('services.show', $service) }}" class="btn btn-outline-secondary btn-sm flex-fill">Details</a>
        <a href="{{ route('services.request', ['service' => $service->slug]) }}" class="btn btn-odin btn-sm flex-fill">Get a Quote</a>
    </div>
</div>
