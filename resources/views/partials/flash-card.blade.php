{{-- Big "flash sale" card with its own countdown, save amount and Buy Now. --}}
<div class="fcard">
    {{-- Yellow & white laser beam that runs around inside the card frame (CSS: .fcard-bolt) --}}
    <svg class="fcard-bolt" aria-hidden="true" focusable="false">
        <rect class="bolt-track" pathLength="100" />
        <g filter="url(#fcardBolt)">
            <rect class="bolt-tail" pathLength="100" />
            <rect class="bolt-body" pathLength="100" />
            <rect class="bolt-core" pathLength="100" />
        </g>
    </svg>
    <a href="{{ route('products.show', $product) }}" class="fcard-media" tabindex="-1" aria-hidden="true">
        <img src="{{ $product->image_url }}" alt="" loading="lazy">
        <span class="off-badge lg"><b>{{ $product->discount_percent }}%</b><small>OFF</small></span>
        <div class="fcard-timer" data-countdown="{{ $endsAt->toIso8601String() }}">
            <span class="fcard-timer-label">Offer<br>Ends In</span>
            <span class="tbox"><b data-part="d">00</b><small>DAYS</small></span>
            <span class="tbox"><b data-part="h">00</b><small>HR</small></span>
            <span class="tbox"><b data-part="m">00</b><small>MIN</small></span>
            <span class="tbox"><b data-part="s">00</b><small>SEC</small></span>
        </div>
    </a>
    <div class="fcard-body">
        <a href="{{ route('products.show', $product) }}" class="fcard-title" title="{{ $product->name }}">{{ $product->name }}</a>
        <div class="fcard-prices">
            <div>
                <del>@money($product->price)</del>
                <strong>@money($product->final_price)</strong>
                @if ($product->monthly_from)<div class="fcard-monthly">Monthly<br><b>@money($product->monthly_from)</b></div>@endif
            </div>
            <div class="fcard-save">SAVE<br><b>@money($product->price - $product->sale_price)</b></div>
        </div>
        <form method="POST" action="{{ route('cart.store', $product) }}">
            @csrf
            <input type="hidden" name="buy_now" value="1">
            <button class="fcard-buy">BUY NOW</button>
        </form>
    </div>
</div>
