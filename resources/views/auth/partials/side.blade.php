<div class="auth-side d-flex flex-column justify-content-between">
    <div>
        @include('partials.logo', ['light' => true])
        <h3 class="fw-bold mt-4">{{ $headline }}</h3>
        <p class="opacity-75">Sri Lanka's technology store for laptops, PCs, printers, CCTV, power banks and audio.</p>
        <ul class="list-unstyled mt-4">
            <li><i class="bi bi-check-circle-fill text-odin-red me-2"></i> Track orders & view order history</li>
            <li><i class="bi bi-check-circle-fill text-odin-red me-2"></i> Save products to your wishlist</li>
            <li><i class="bi bi-check-circle-fill text-odin-red me-2"></i> Faster checkout with saved address</li>
            <li><i class="bi bi-check-circle-fill text-odin-red me-2"></i> Exclusive member-only deals</li>
        </ul>
    </div>
    <div class="small opacity-75 position-relative"><i class="bi bi-telephone me-1"></i> Need help? {{ config('shop.hotline') }}</div>
</div>
