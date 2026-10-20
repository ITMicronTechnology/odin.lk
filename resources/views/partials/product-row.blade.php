{{--
    A sliding row of products (Abans style): title + VIEW ALL, arrows, swipe on phones.
    Params: $title, $products, optional $link, $icon, $chips (links under the title), $flash (use flash cards), $endsAt.
--}}
<section class="prow {{ ($flash ?? false) ? 'prow-flash' : '' }}">
    @if ($flash ?? false)
        {{-- Laser glow for the beam that runs around each flash card (used by partials/flash-card) --}}
        <svg class="fcard-bolt-defs" width="0" height="0" aria-hidden="true" focusable="false">
            <filter id="fcardBolt" x="-4%" y="-4%" width="108%" height="108%">
                <feGaussianBlur in="SourceGraphic" stdDeviation="3.5" result="bloom" />
                <feGaussianBlur in="SourceGraphic" stdDeviation="1" result="halo" />
                <feMerge><feMergeNode in="bloom" /><feMergeNode in="bloom" /><feMergeNode in="halo" /><feMergeNode in="halo" /><feMergeNode in="SourceGraphic" /></feMerge>
            </filter>
        </svg>
    @endif
    <div class="container">
        <div class="prow-head">
            <h2>@isset($icon)<i class="bi {{ $icon }}"></i>@endisset {{ $title }}</h2>
            @isset($link)<a href="{{ $link }}" class="prow-all">VIEW ALL <i class="bi bi-chevron-right"></i></a>@endisset
        </div>
        @if (! empty($chips))
            <div class="prow-chips">
                @foreach ($chips as $chipLabel => $chipLink)
                    <a href="{{ $chipLink }}">{{ $chipLabel }}</a>
                @endforeach
            </div>
        @endif
        <div class="prow-slider" data-slider>
            <button type="button" class="prow-nav prev" data-slide="-1" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
            <div class="prow-track" data-track>
                @foreach ($products as $product)
                    <div class="prow-item">
                        @if ($flash ?? false)
                            @include('partials.flash-card', ['product' => $product, 'endsAt' => $endsAt])
                        @else
                            @include('partials.product-card', ['product' => $product])
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" class="prow-nav next" data-slide="1" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</section>
