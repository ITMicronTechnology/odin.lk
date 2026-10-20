{{--
    Customer Ratings & Reviews on the product page.
    The first reviews are drawn here; app.js posts new reviews to the JSON API and refreshes this section without reloading the page.
    Params: $product, $ratingSummary, $reviews, $myReview.
--}}
@use('App\Http\Requests\StoreProductReviewRequest')
@use('App\Models\ProductReview')
@php($reviewCount = $ratingSummary['count'])
<section class="reviews-section" id="reviews"
         data-reviews
         data-list-url="{{ route('api.products.reviews.index', $product) }}"
         data-rating-url="{{ route('api.products.rating', $product) }}"
         data-store-url="{{ route('api.products.reviews.store', $product) }}">
    <div class="container">
        <div class="prow-head">
            <h2><i class="bi bi-star-fill"></i> Customer Ratings &amp; Reviews</h2>
        </div>

        <div class="reviews-grid">
            <div class="reviews-aside">
                {{-- Overall rating and 5 → 1 star breakdown --}}
                <div class="rating-summary">
                    <div class="rating-score">
                        <strong data-rating-average>{{ number_format($ratingSummary['average'], 1) }}</strong><span>/ 5</span>
                    </div>
                    <div data-rating-stars>@include('partials.stars', ['rating' => $ratingSummary['average'], 'class' => 'lg'])</div>
                    <p class="rating-based" data-rating-count>
                        {{ $reviewCount ? 'Based on '.number_format($reviewCount).' '.\Illuminate\Support\Str::plural('review', $reviewCount) : 'No reviews yet' }}
                    </p>
                    <ul class="rating-bars">
                        @foreach ($ratingSummary['distribution'] as $row)
                            <li data-rating-row="{{ $row['stars'] }}">
                                <span class="rating-bar-label">{{ $row['stars'] }} <i class="bi bi-star-fill"></i></span>
                                <span class="rating-bar" role="progressbar" aria-label="{{ $row['stars'] }} star reviews" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $row['percent'] }}"><i style="width: {{ $row['percent'] }}%"></i></span>
                                <span class="rating-bar-count"><b data-count>{{ $row['count'] }}</b> <small data-percent>({{ $row['percent'] }}%)</small></span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Write / edit a review --}}
                <div class="review-form-box">
                    @auth
                        <h3 data-review-form-title>{{ $myReview ? 'Edit your review' : 'Write a Review' }}</h3>
                        <p class="small text-muted mb-3" data-review-form-note>
                            {{ $myReview ? 'You have already reviewed this product. Changes replace your earlier review.' : 'Share your experience to help other customers.' }}
                        </p>
                        <form data-review-form data-has-review="{{ $myReview ? 'true' : 'false' }}" novalidate>
                            <fieldset class="mb-1">
                                <legend class="form-label small fw-semibold mb-1">Your rating <span class="text-danger">*</span></legend>
                                <div class="star-input-wrap">
                                    <div class="star-input" data-star-input>
                                        @for ($star = ProductReview::MAX_RATING; $star >= ProductReview::MIN_RATING; $star--)
                                            <input type="radio" name="rating" value="{{ $star }}" id="rate-{{ $star }}" @checked($myReview?->rating === $star)>
                                            <label for="rate-{{ $star }}" data-value="{{ $star }}" title="{{ $star }} {{ \Illuminate\Support\Str::plural('star', $star) }}"><i class="bi bi-star-fill"></i><span class="visually-hidden">{{ $star }} {{ \Illuminate\Support\Str::plural('star', $star) }}</span></label>
                                        @endfor
                                    </div>
                                    <span class="star-hint" data-star-hint>Select a rating</span>
                                </div>
                            </fieldset>
                            <div class="review-error" data-error="rating"></div>

                            <label for="reviewComment" class="form-label small fw-semibold mt-2 mb-1">Your review <span class="text-danger">*</span></label>
                            <textarea id="reviewComment" name="comment" class="form-control" rows="4"
                                      minlength="{{ StoreProductReviewRequest::COMMENT_MIN }}" maxlength="{{ StoreProductReviewRequest::COMMENT_MAX }}"
                                      placeholder="What did you like or dislike? How is the quality, performance and value?">{{ $myReview?->comment }}</textarea>
                            <div class="d-flex justify-content-between gap-2 mt-1">
                                <div class="review-error" data-error="comment"></div>
                                <small class="text-muted text-nowrap" data-comment-count>0 / {{ StoreProductReviewRequest::COMMENT_MAX }}</small>
                            </div>

                            <div class="review-alert" data-review-alert role="alert" hidden></div>

                            <button type="submit" class="btn btn-odin w-100 mt-2" data-review-submit>
                                <span class="spinner-border spinner-border-sm me-1 d-none" aria-hidden="true"></span>
                                <span data-label>{{ $myReview ? 'Update Review' : 'Submit Review' }}</span>
                            </button>
                        </form>
                    @else
                        <div class="review-login">
                            <i class="bi bi-chat-square-heart"></i>
                            <p>Please log in to write a review.</p>
                            <a href="{{ route('login', ['redirect' => route('products.show', $product).'#reviews']) }}" class="btn btn-odin px-4">Log in</a>
                            <small>New to Odin.lk? <a href="{{ route('register') }}">Create an account</a></small>
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Reviews list, newest first --}}
            <div class="reviews-main">
                <div class="reviews-list-head">
                    <h3>Customer Reviews <span data-review-total>({{ number_format($reviewCount) }})</span></h3>
                    <small class="text-muted">Newest first</small>
                </div>
                <div class="reviews-list" data-review-list aria-live="polite">
                    @foreach ($reviews as $review)
                        @include('partials.review-card', ['review' => $review])
                    @endforeach
                </div>
                <div class="reviews-empty {{ $reviewCount ? 'd-none' : '' }}" data-review-empty>
                    <i class="bi bi-chat-left-text"></i>
                    <p class="mb-0">No reviews yet. Be the first to review this product!</p>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 mt-3 {{ $reviewCount > $reviews->count() ? '' : 'd-none' }}" data-review-more data-next-page="2">
                        <span class="spinner-border spinner-border-sm me-1 d-none" aria-hidden="true"></span> Load more reviews
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
