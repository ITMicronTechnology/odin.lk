<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_COMMENT = 'Great laptop, fast and the battery lasts all day.';

    public function test_product_page_shows_ratings_section_and_login_prompt_for_guests(): void
    {
        $product = Product::factory()->create();
        ProductReview::factory()->for($product)->rating(5)->create(['comment' => 'Superb sound quality for the price.']);
        ProductReview::factory()->for($product)->rating(4)->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Customer Ratings &amp; Reviews', false)
            ->assertSee('4.5')
            ->assertSee('Based on 2 reviews')
            ->assertSee('Superb sound quality for the price.')
            ->assertSee('Please log in to write a review.')
            ->assertDontSee('data-review-form', false);
    }

    public function test_signed_in_customer_sees_the_form_and_their_existing_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Write a Review')
            ->assertSee('Submit Review')
            ->assertSee('No reviews yet');

        ProductReview::factory()->for($product)->for($user)->rating(3)->create(['comment' => 'It is okay for daily use.']);

        $this->actingAs($user)->get(route('products.show', $product))
            ->assertSee('Edit your review')
            ->assertSee('Update Review')
            ->assertSee('It is okay for daily use.')
            ->assertSee('value="3" id="rate-3" checked', false);
    }

    public function test_reviews_api_lists_newest_first_with_pagination(): void
    {
        $product = Product::factory()->create();
        ProductReview::factory()->for($product)->create(['comment' => 'Oldest review here.', 'created_at' => now()->subDays(3)]);
        ProductReview::factory()->for($product)->create(['comment' => 'Newest review here.', 'created_at' => now()]);
        ProductReview::factory()->count(ProductReview::PER_PAGE)->for($product)->create(['created_at' => now()->subDay()]);
        ProductReview::factory()->create(['comment' => 'Review of another product.']);

        $response = $this->getJson(route('api.products.reviews.index', $product))
            ->assertOk()
            ->assertJsonCount(ProductReview::PER_PAGE, 'data')
            ->assertJsonPath('data.0.comment', 'Newest review here.')
            ->assertJsonPath('meta.total', ProductReview::PER_PAGE + 2)
            ->assertJsonStructure(['data' => [['id', 'rating', 'comment', 'user' => ['name'], 'is_mine', 'created_at', 'date']], 'meta' => ['current_page', 'last_page', 'total']]);

        $this->assertStringNotContainsString('Review of another product.', $response->getContent());

        $this->getJson(route('api.products.reviews.index', ['product' => $product, 'page' => 2]))
            ->assertOk()
            ->assertJsonPath('data.1.comment', 'Oldest review here.');
    }

    public function test_rating_api_returns_average_count_and_distribution(): void
    {
        $product = Product::factory()->create();
        foreach ([5, 5, 4, 1] as $rating) {
            ProductReview::factory()->for($product)->rating($rating)->create();
        }

        $this->getJson(route('api.products.rating', $product))
            ->assertOk()
            ->assertJsonPath('data.average', 3.8)
            ->assertJsonPath('data.count', 4)
            ->assertJsonPath('data.distribution', [
                ['stars' => 5, 'count' => 2, 'percent' => 50],
                ['stars' => 4, 'count' => 1, 'percent' => 25],
                ['stars' => 3, 'count' => 0, 'percent' => 0],
                ['stars' => 2, 'count' => 0, 'percent' => 0],
                ['stars' => 1, 'count' => 1, 'percent' => 25],
            ]);

        $this->getJson(route('api.products.rating', Product::factory()->create()))
            ->assertJsonPath('data.average', fn ($average) => $average == 0)
            ->assertJsonPath('data.count', 0);
    }

    public function test_guests_cannot_post_reviews(): void
    {
        $product = Product::factory()->create();

        $this->postJson(route('api.products.reviews.store', $product), ['rating' => 5, 'comment' => self::VALID_COMMENT])
            ->assertUnauthorized();

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_customer_can_post_a_review_as_themselves_only(): void
    {
        $user = User::factory()->create(['name' => 'Kasun Perera']);
        $someoneElse = User::factory()->create();
        $product = Product::factory()->create();
        ProductReview::factory()->for($product)->rating(1)->create();

        $this->actingAs($user)
            ->postJson(route('api.products.reviews.store', $product), [
                'rating' => 5,
                'comment' => self::VALID_COMMENT,
                'user_id' => $someoneElse->id,
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Thank you! Your review has been posted.')
            ->assertJsonPath('data.user.name', 'Kasun Perera')
            ->assertJsonPath('data.is_mine', true)
            ->assertJsonPath('summary.average', fn ($average) => $average == 3)
            ->assertJsonPath('summary.count', 2);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => self::VALID_COMMENT,
        ]);
        $this->assertDatabaseMissing('product_reviews', ['user_id' => $someoneElse->id]);

        $this->getJson(route('api.products.reviews.index', $product))->assertJsonPath('data.0.comment', self::VALID_COMMENT);
    }

    public function test_posting_again_updates_the_customers_existing_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('api.products.reviews.store', $product), ['rating' => 2, 'comment' => 'Not great at first sight.'])->assertCreated();

        $this->actingAs($user)
            ->postJson(route('api.products.reviews.store', $product), ['rating' => 4, 'comment' => '  Much better after the update.  '])
            ->assertOk()
            ->assertJsonPath('message', 'Your review has been updated.')
            ->assertJsonPath('summary.average', fn ($average) => $average == 4)
            ->assertJsonPath('summary.count', 1);

        $this->assertDatabaseCount('product_reviews', 1);
        $this->assertDatabaseHas('product_reviews', ['user_id' => $user->id, 'rating' => 4, 'comment' => 'Much better after the update.']);
    }

    /**
     * @return array<string, array{array<string, mixed>, string}>
     */
    public static function invalidReviews(): array
    {
        return [
            'missing rating' => [['comment' => self::VALID_COMMENT], 'rating'],
            'rating zero' => [['rating' => 0, 'comment' => self::VALID_COMMENT], 'rating'],
            'rating six' => [['rating' => 6, 'comment' => self::VALID_COMMENT], 'rating'],
            'half star' => [['rating' => 4.5, 'comment' => self::VALID_COMMENT], 'rating'],
            'text rating' => [['rating' => 'five', 'comment' => self::VALID_COMMENT], 'rating'],
            'missing comment' => [['rating' => 5], 'comment'],
            'blank comment' => [['rating' => 5, 'comment' => '        '], 'comment'],
            'short comment' => [['rating' => 5, 'comment' => 'Good'], 'comment'],
            'no words' => [['rating' => 5, 'comment' => '!!!!!!!!!!!! 12345'], 'comment'],
            'too long' => [['rating' => 5, 'comment' => str_repeat('a', 2001)], 'comment'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidReviews')]
    public function test_invalid_reviews_are_rejected(array $payload, string $field): void
    {
        $product = Product::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('api.products.reviews.store', $product), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);

        $this->assertDatabaseCount('product_reviews', 0);
    }

    public function test_inactive_products_cannot_be_reviewed(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->getJson(route('api.products.reviews.index', $product))->assertNotFound();
        $this->getJson(route('api.products.rating', $product))->assertNotFound();
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.products.reviews.store', $product), ['rating' => 5, 'comment' => self::VALID_COMMENT])
            ->assertNotFound();
    }

    public function test_admin_can_list_and_delete_reviews(): void
    {
        $review = ProductReview::factory()->create(['comment' => 'Spam spam spam review.']);

        $this->actingAs(User::factory()->create())->get(route('admin.reviews.index'))->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.reviews.index'))->assertOk()->assertSee('Spam spam spam review.');
        $this->actingAs($admin)->delete(route('admin.reviews.destroy', $review))->assertRedirect();

        $this->assertModelMissing($review);
    }

    public function test_login_link_returns_customer_to_the_product_but_ignores_other_sites(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $productUrl = route('products.show', $product).'#reviews';

        $this->get(route('login', ['redirect' => $productUrl]))->assertOk();
        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect($productUrl);

        auth()->logout();
        $this->flushSession();

        $this->get(route('login', ['redirect' => 'https://evil.example.com/phish']))->assertOk();
        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('home'));
    }
}
