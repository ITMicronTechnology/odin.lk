<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\GeoIpResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisitorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private const CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36';

    private const IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1';

    public function test_storefront_page_views_are_recorded(): void
    {
        $this->withHeader('User-Agent', self::IPHONE)
            ->withHeader('Referer', 'https://www.facebook.com/odin')
            ->get(route('home'))
            ->assertOk()
            ->assertCookie(config('analytics.cookie'));

        $visitor = Visitor::sole();
        $this->assertSame('mobile', $visitor->device_type);
        $this->assertSame('Safari', $visitor->browser);
        $this->assertSame('iOS', $visitor->os);

        $visit = Visit::sole();
        $this->assertSame('/', $visit->landing_path);
        $this->assertSame('www.facebook.com', $visit->referrer_host);
        $this->assertSame('home', PageView::sole()->route_name);
    }

    public function test_returning_visitor_keeps_the_same_visit_until_it_times_out(): void
    {
        $this->withHeader('User-Agent', self::CHROME)->get(route('home'));
        $uuid = Visitor::sole()->uuid;

        $this->withHeader('User-Agent', self::CHROME)->withCookie(config('analytics.cookie'), $uuid)->get(route('shop.index'));
        $this->assertSame(1, Visit::count());
        $this->assertSame(2, Visit::sole()->page_views_count);

        $this->travel(31)->minutes();
        $this->withHeader('User-Agent', self::CHROME)->withCookie(config('analytics.cookie'), $uuid)->get(route('shop.deals'));

        $this->assertSame(1, Visitor::count());
        $this->assertSame(2, Visit::count());
        $this->assertSame(2, Visitor::sole()->visits_count);
    }

    public function test_bots_admins_admin_pages_and_background_requests_are_not_counted(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')->get(route('home'));
        $this->withHeader('User-Agent', self::CHROME)->getJson(route('search.suggest', ['q' => 'laptop']));
        $this->withHeader('User-Agent', self::CHROME)->get('/page-that-does-not-exist');

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->withHeader('User-Agent', self::CHROME)->get(route('home'));
        $this->actingAs($admin)->withHeader('User-Agent', self::CHROME)->get(route('admin.dashboard'));

        $this->assertSame(0, PageView::count());
    }

    public function test_admin_can_view_the_analytics_page_and_live_numbers(): void
    {
        $this->withHeader('User-Agent', self::IPHONE)->get(route('home'));
        $this->withHeader('User-Agent', self::CHROME)->get(route('shop.index'));

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.analytics.index', ['range' => '7d']))
            ->assertOk()
            ->assertSee('Visitor Analytics')
            ->assertSee('Mobile vs desktop')
            ->assertSee('Local network')
            ->assertSee('Shop / Search');

        $this->actingAs($admin)->getJson(route('admin.analytics.live'))
            ->assertOk()
            ->assertJsonPath('online', 2)
            ->assertJsonPath('today', 2)
            ->assertJsonCount(2, 'visits');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('online now');
    }

    public function test_customers_cannot_see_analytics(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.analytics.index'))->assertForbidden();
    }

    public function test_countries_are_resolved_from_ip_addresses(): void
    {
        Http::fake([
            'ip-api.com/*' => Http::response([
                ['status' => 'success', 'countryCode' => 'LK', 'country' => 'Sri Lanka', 'query' => '112.134.10.5'],
            ]),
        ]);

        $local = $this->makeVisitor('127.0.0.1');
        $sriLankan = $this->makeVisitor('112.134.10.5');

        app(GeoIpResolver::class)->resolvePending();

        $this->assertSame(GeoIpResolver::LOCAL, $local->fresh()->country_code);
        $this->assertSame('LK', $sriLankan->fresh()->country_code);
        $this->assertSame('LK', $sriLankan->visits()->sole()->country_code);
    }

    public function test_cloudflare_country_header_is_used_when_present(): void
    {
        $this->withHeaders(['User-Agent' => self::CHROME, 'CF-IPCountry' => 'lk'])->get(route('home'));

        $this->assertSame('LK', Visitor::sole()->country_code);
        $this->assertSame('LK', Visit::sole()->country_code);
    }

    public function test_old_analytics_data_can_be_pruned(): void
    {
        $old = $this->makeVisitor('10.0.0.1');
        $old->forceFill(['last_seen_at' => now()->subDays(500)])->save();
        $old->visits()->update(['last_activity_at' => now()->subDays(500)]);
        $recent = $this->makeVisitor('10.0.0.2');

        $this->artisan('analytics:prune')->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
    }

    private function makeVisitor(string $ip): Visitor
    {
        $visitor = Visitor::create([
            'uuid' => fake()->uuid(), 'ip' => $ip, 'device_type' => 'desktop', 'browser' => 'Chrome', 'os' => 'Windows',
            'first_seen_at' => now(), 'last_seen_at' => now(), 'visits_count' => 1,
        ]);
        $visitor->visits()->create([
            'device_type' => 'desktop', 'browser' => 'Chrome', 'os' => 'Windows', 'landing_path' => '/',
            'page_views_count' => 1, 'started_at' => now(), 'last_activity_at' => now(),
        ]);

        return $visitor;
    }
}
