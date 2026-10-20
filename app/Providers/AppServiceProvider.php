<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Enums\ServiceInquiryStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\ServiceInquiry;
use App\Services\Cart;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Cart::class, fn ($app) => new Cart($app['session.store']));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(
            Str::lower($request->string('email')).'|'.$request->ip()
        ));

        RateLimiter::for('order-tracking', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('service-requests', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));
        RateLimiter::for('reviews', fn (Request $request) => Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()));

        Blade::directive('money', fn (string $expression): string => "<?php echo e(config('shop.currency').' '.number_format((float) ({$expression}), 2)); ?>");

        View::composer('partials.product-card', function ($view): void {
            $view->with('wishlistIds', once(fn (): array => auth()->user()?->wishlist()->pluck('products.id')->all() ?? []));
        });

        View::composer('admin.layout', function ($view): void {
            $view->with([
                'pendingOrders' => Order::where('status', OrderStatus::Pending)->count(),
                'newServiceRequests' => ServiceInquiry::where('status', ServiceInquiryStatus::New)->count(),
            ]);
        });

        View::composer('layouts.app', function ($view): void {
            $view->with([
                'navCategories' => Category::active()->roots()
                    ->with(['children' => fn ($query) => $query->active()])
                    ->orderBy('sort_order')
                    ->get(),
                'cartCount' => app(Cart::class)->count(),
                'cartSubtotal' => app(Cart::class)->isEmpty() ? 0 : app(Cart::class)->subtotal(),
                'wishlistCount' => auth()->user()?->wishlist()->count() ?? 0,
            ]);
        });
    }
}
