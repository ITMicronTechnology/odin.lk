<?php

namespace App\Http\Requests;

use App\Models\ProductReview;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public const COMMENT_MIN = 10;

    public const COMMENT_MAX = 2000;

    /**
     * Guests are stopped by the auth middleware; any signed-in customer may review.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * The reviewer is always the signed-in user, so user_id is never read from the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:'.ProductReview::MIN_RATING.','.ProductReview::MAX_RATING],
            'comment' => ['required', 'string', 'min:'.self::COMMENT_MIN, 'max:'.self::COMMENT_MAX, 'regex:/\pL/u'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Please select a star rating.',
            'rating.integer' => 'Please select a star rating from 1 to 5.',
            'rating.between' => 'Please select a star rating from 1 to 5.',
            'comment.required' => 'Please write a few words about the product.',
            'comment.min' => 'Your review must be at least '.self::COMMENT_MIN.' characters.',
            'comment.max' => 'Your review may not be longer than '.self::COMMENT_MAX.' characters.',
            'comment.regex' => 'Please write your review in words.',
        ];
    }
}
