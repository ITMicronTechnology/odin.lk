<?php

namespace App\Services;

use App\Models\PageView;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Locale;

/**
 * Numbers and breakdowns for the admin "Visitor Analytics" page.
 */
class VisitorStats
{
    /**
     * Period filters: key => [label, days back (null = all time)].
     *
     * @var array<string, array{0: string, 1: int|null}>
     */
    public const RANGES = [
        'today' => ['Today', 0],
        '7d' => ['Last 7 days', 6],
        '30d' => ['Last 30 days', 29],
        '90d' => ['Last 90 days', 89],
        'all' => ['All time', null],
    ];

    /**
     * Friendly names for storefront routes.
     *
     * @var array<string, string>
     */
    private const PAGE_LABELS = [
        'home' => 'Home', 'shop.index' => 'Shop / Search', 'shop.deals' => 'Hot Deals', 'shop.category' => 'Category',
        'shop.brand' => 'Brand', 'products.show' => 'Product', 'cart.index' => 'Cart', 'checkout.create' => 'Checkout',
        'checkout.success' => 'Order placed', 'orders.track' => 'Track Order', 'login' => 'Login', 'register' => 'Register',
        'wishlist.index' => 'Wishlist', 'account.index' => 'My Account', 'account.orders' => 'My Orders',
        'account.orders.show' => 'Order details', 'account.profile' => 'Profile',
        'services.index' => 'IT Services', 'services.show' => 'Service', 'services.request' => 'Quote request',
        'services.thanks' => 'Quote sent', 'account.services' => 'My Service Requests',
    ];

    public function since(string $range): ?Carbon
    {
        $days = self::RANGES[$range][1] ?? 29;

        return $days === null ? null : today()->subDays($days);
    }

    /**
     * Headline numbers: totals, online now and daily / weekly / monthly active visitors.
     *
     * @return array<string, int|float>
     */
    public function summary(?Carbon $since): array
    {
        $visits = Visit::when($since, fn ($query) => $query->where('started_at', '>=', $since));
        $totalVisits = (clone $visits)->count();
        $singlePageVisits = (clone $visits)->where('page_views_count', 1)->count();

        return [
            'total_visitors' => Visitor::count(),
            'range_visitors' => (clone $visits)->distinct('visitor_id')->count('visitor_id'),
            'new_visitors' => Visitor::when($since, fn ($query) => $query->where('first_seen_at', '>=', $since))->count(),
            'total_visits' => $totalVisits,
            'page_views' => PageView::when($since, fn ($query) => $query->where('created_at', '>=', $since))->count(),
            'online' => $this->onlineCount(),
            'daily' => $this->activeVisitorsSince(today()),
            'weekly' => $this->activeVisitorsSince(today()->subDays(6)),
            'monthly' => $this->activeVisitorsSince(today()->subDays(29)),
            'registered_customers' => User::where('role', 'customer')->count(),
            'logged_in_visitors' => Visitor::whereNotNull('user_id')->count(),
            'pages_per_visit' => $totalVisits ? round((clone $visits)->avg('page_views_count'), 1) : 0,
            'bounce_rate' => $totalVisits ? round($singlePageVisits / $totalVisits * 100) : 0,
        ];
    }

    public function onlineCount(): int
    {
        return Visitor::where('last_seen_at', '>=', now()->subMinutes(config('analytics.online_minutes')))->count();
    }

    /**
     * Visits happening right now, with the page each visitor is currently on.
     *
     * @return Collection<int, Visit>
     */
    public function onlineVisits(int $limit = 12): Collection
    {
        return Visit::with('visitor')
            ->where('last_activity_at', '>=', now()->subMinutes(config('analytics.online_minutes')))
            ->addSelect(['current_path' => PageView::select('path')
                ->whereColumn('visit_id', 'visits.id')
                ->latest('id')
                ->limit(1)])
            ->latest('last_activity_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Daily unique visitors, visits and page views for the chart.
     *
     * @return Collection<int, array{label: string, visitors: int, visits: int, views: int}>
     */
    public function daily(?Carbon $since): Collection
    {
        // "All time" charts the last 90 days; "Today" shows the last 2 weeks for context.
        $from = match (true) {
            $since === null => today()->subDays(89),
            $since->isToday() => today()->subDays(13),
            default => $since,
        };

        $views = PageView::where('created_at', '>=', $from)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $visits = Visit::where('started_at', '>=', $from)
            ->select(DB::raw('DATE(started_at) as day'), DB::raw('COUNT(*) as visits'))
            ->groupBy('day')
            ->pluck('visits', 'day');

        return collect(range(0, $from->diffInDays(today())))->map(function (int $offset) use ($from, $views, $visits): array {
            $day = $from->copy()->addDays($offset)->toDateString();

            return [
                'label' => Carbon::parse($day)->format('M d'),
                'visitors' => (int) ($views[$day]->visitors ?? 0),
                'visits' => (int) ($visits[$day] ?? 0),
                'views' => (int) ($views[$day]->views ?? 0),
            ];
        });
    }

    /**
     * Visits and unique visitors grouped by a visits column (country, device, browser, os, referrer).
     *
     * @return Collection<int, object{name: string|null, visits: int, visitors: int}>
     */
    public function breakdown(string $column, ?Carbon $since, int $limit = 10): Collection
    {
        return Visit::when($since, fn ($query) => $query->where('started_at', '>=', $since))
            ->select("{$column} as name", DB::raw('COUNT(*) as visits'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy($column)
            ->orderByDesc('visits')
            ->limit($limit)
            ->toBase()
            ->get();
    }

    /**
     * @return Collection<int, object{name: string|null, flag: string, label: string, visits: int, visitors: int}>
     */
    public function countries(?Carbon $since, int $limit = 15): Collection
    {
        return $this->breakdown('country_code', $since, $limit)->map(function (object $row): object {
            $row->flag = $this->flag($row->name);
            $row->label = $this->countryName($row->name);

            return $row;
        });
    }

    /**
     * Most viewed storefront pages.
     *
     * @return Collection<int, object{path: string, route_name: string|null, label: string, views: int, visitors: int}>
     */
    public function topPages(?Carbon $since, int $limit = 20): Collection
    {
        return PageView::when($since, fn ($query) => $query->where('created_at', '>=', $since))
            ->select('path', DB::raw('MAX(route_name) as route_name'), DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_id) as visitors'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit)
            ->toBase()
            ->get()
            ->map(function (object $row): object {
                $row->label = self::PAGE_LABELS[$row->route_name] ?? 'Page';

                return $row;
            });
    }

    /**
     * @return Collection<int, Visit>
     */
    public function recentVisits(int $limit = 15): Collection
    {
        return Visit::with('visitor.user')->latest('started_at')->limit($limit)->get();
    }

    public function activeVisitorsSince(Carbon $since): int
    {
        return PageView::where('created_at', '>=', $since)->distinct('visitor_id')->count('visitor_id');
    }

    public function flag(?string $code): string
    {
        if ($code === null) {
            return '🌐';
        }

        if ($code === GeoIpResolver::LOCAL) {
            return '🏠';
        }

        return implode('', array_map(fn (string $letter): string => mb_chr(0x1F1E6 + ord($letter) - 65), str_split($code)));
    }

    public function countryName(?string $code): string
    {
        return match (true) {
            $code === null => 'Unknown',
            $code === GeoIpResolver::LOCAL => 'Local network',
            class_exists(Locale::class) => Locale::getDisplayRegion('-'.$code, 'en') ?: $code,
            default => $code,
        };
    }
}
