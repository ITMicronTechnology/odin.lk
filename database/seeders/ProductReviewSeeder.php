<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample customer reviews so product pages show ratings out of the box. Safe to run again: it skips products that already have reviews.
 */
class ProductReviewSeeder extends Seeder
{
    /**
     * @var array<int, list<string>>
     */
    private const COMMENTS = [
        5 => [
            'Excellent product! Works exactly as described and the build quality is superb. Delivery to my home was quick too.',
            'Genuine item with official warranty. Setup took only a few minutes. Highly recommend Odin.lk.',
            'Very happy with this purchase. Performance is great and the price was the best I found in Sri Lanka.',
            'Ordered on Monday and received it on Tuesday in Colombo. Well packed and brand new. Five stars!',
            'Using it every day for a month now and no issues at all. Worth every rupee.',
        ],
        4 => [
            'Good product for the price. Does everything I need, only the manual could be clearer.',
            'Works well and looks premium. Delivery took three days to Kandy but the staff kept me updated.',
            'Solid quality and good performance. Would have given five stars if it came with a better cable.',
            'Nice product and genuine warranty card included. Happy overall.',
        ],
        3 => [
            'It is okay. Does the job but I expected a little better quality at this price.',
            'Average experience. The product works fine but the box was slightly damaged on delivery.',
        ],
        2 => [
            'Not as good as I expected. Performance is below what the specifications suggest.',
        ],
        1 => [
            'Had a problem with the unit I received. The support team is arranging a replacement under warranty.',
        ],
    ];

    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            return;
        }

        mt_srand(2026);

        Product::active()->whereDoesntHave('reviews')->orderBy('id')->get()
            ->filter(fn (Product $product): bool => $product->is_featured || mt_rand(1, 100) <= 55)
            ->each(function (Product $product) use ($customers): void {
                $reviewers = $customers->shuffle(mt_rand())->take(mt_rand(1, $customers->count()));

                foreach ($reviewers as $customer) {
                    $rating = [5, 5, 5, 5, 4, 4, 4, 3, 2, 1][mt_rand(0, 9)];
                    $comments = self::COMMENTS[$rating];
                    $date = now()->subDays(mt_rand(1, 120))->subMinutes(mt_rand(0, 1440));

                    $review = new ProductReview([
                        'rating' => $rating,
                        'comment' => $comments[mt_rand(0, count($comments) - 1)],
                    ]);
                    $review->product()->associate($product);
                    $review->user()->associate($customer);
                    $review->created_at = $date;
                    $review->updated_at = $date;
                    $review->save();
                }
            });
    }
}
