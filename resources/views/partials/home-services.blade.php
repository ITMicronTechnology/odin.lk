<section class="home-block">
    <div class="container">
        <div class="svc-home">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <span class="svc-eyebrow"><i class="bi bi-stars me-1"></i> ODIN IT SERVICES</span>
                    <h2 class="fw-bold mb-1 mt-2">Grow your business with the right software</h2>
                    <p class="mb-0 opacity-75">Websites, ERP and business systems built and supported by our team in Colombo.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('services.portfolio') }}" class="btn btn-outline-light"><i class="bi bi-file-earmark-pdf me-1"></i> Download Portfolio</a>
                    <a href="{{ route('services.request') }}" class="btn btn-odin"><i class="bi bi-send me-1"></i> Request a Free Quote</a>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($serviceCategories as $serviceCategory)
                    <div class="col-sm-6 col-lg-3">
                        <a href="{{ route('services.index') }}#{{ $serviceCategory->slug }}" class="svc-home-tile">
                            <i class="bi {{ $serviceCategory->icon ?: 'bi-gear' }}"></i>
                            <h6>{{ $serviceCategory->name }}</h6>
                            <p>{{ $serviceCategory->tagline }}</p>
                            <span>{{ $serviceCategory->services_count }} solutions <i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
