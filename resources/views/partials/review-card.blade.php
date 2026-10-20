{{-- One customer review. Mirrored by reviewCard() in app.js, which draws new cards after a review is posted. --}}
<article class="review-card {{ auth()->id() === $review->user_id ? 'mine' : '' }}">
    <div class="review-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($review->user->name, 0, 1)) }}</div>
    <div class="review-body">
        <div class="review-top">
            <strong>{{ $review->user->name }}</strong>
            @if (auth()->id() === $review->user_id)
                <span class="review-you">Your review</span>
            @endif
            <time datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->format('d M Y') }}</time>
        </div>
        @include('partials.stars', ['rating' => $review->rating])
        <p>{{ $review->comment }}</p>
        @if ($review->updated_at->gt($review->created_at))
            <small class="review-edited">Edited</small>
        @endif
    </div>
</article>
