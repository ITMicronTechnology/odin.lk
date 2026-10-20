@php($isWishlisted = in_array($product->id, $wishlistIds, true))
<div class="pcard {{ $product->inStock() ? '' : 'is-out' }}">
    <a href="{{ route('products.show', $product) }}" class="pcard-media" tabindex="-1" aria-hidden="true">
        <img src="{{ $product->image_url }}" alt="" loading="lazy">
        @if ($product->isOnSale())
            <span class="off-badge"><b>{{ $product->discount_percent }}%</b><small>OFF</small></span>
        @endif
    </a>
    <button type="button" class="btn-wishlist {{ $isWishlisted ? 'active' : '' }}" data-url="{{ route('wishlist.toggle', $product) }}" aria-label="Add {{ $product->name }} to wishlist">
        <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
    </button>
    <div class="pcard-body">
        <a href="{{ route('products.show', $product) }}" class="pcard-title" title="{{ $product->name }}">{{ $product->name }}</a>
        <div class="pcard-price">
            @if ($product->isOnSale())<del>@money($product->price)</del>@endif
            <strong>@money($product->final_price)</strong>
        </div>
        @if ($product->monthly_from)
            <div class="pcard-monthly">Monthly <b>@money($product->monthly_from)</b></div>
        @endif
        @if ($tags = $product->tags())
            <div class="pcard-tags">
                @foreach ($tags as $tag)<span class="ptag ptag-{{ $tag['style'] }}">{{ $tag['label'] }}</span>@endforeach
            </div>
        @endif
    </div>
    <div class="pcard-action">
        @if ($product->inStock())
            <button type="button" class="btn-add-cart" data-url="{{ route('cart.store', $product) }}"><i class="bi bi-bag-plus me-1"></i> Add to Cart</button>
        @else
            <a href="{{ route('products.show', $product) }}" class="btn-view">View Details</a>
        @endif
    </div>
</div>
