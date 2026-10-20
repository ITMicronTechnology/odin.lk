{{-- Read-only star rating. Params: $rating (0 - 5, halves allowed), optional $class. Mirrored by starsHtml() in app.js. --}}
@php($rounded = round(($rating ?? 0) * 2) / 2)
<span class="stars {{ $class ?? '' }}" role="img" aria-label="{{ number_format($rating ?? 0, 1) }} out of 5 stars">
    @for ($star = 1; $star <= 5; $star++)
        <i class="bi {{ $rounded >= $star ? 'bi-star-fill' : ($rounded >= $star - 0.5 ? 'bi-star-half' : 'bi-star') }}"></i>
    @endfor
</span>
