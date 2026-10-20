<?php

namespace App\Services;

use App\Models\PageView;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records storefront page views as visitors, visits (sessions) and page views.
 */
class VisitorTracker
{
    /**
     * Paths that are never counted as page views.
     *
     * @var list<string>
     */
    private const IGNORED_PATHS = ['admin', 'admin/*', 'up', '_boost/*', 'storage/*', 'search/suggest', 'build/*', 'vendor/*'];

    public function __construct(private UserAgentParser $parser) {}

    /**
     * The visitor id from the cookie, or a new one when the browser has none yet.
     */
    public function visitorUuid(Request $request): string
    {
        $uuid = (string) $request->cookie(config('analytics.cookie'));

        return Str::isUuid($uuid) ? $uuid : (string) Str::uuid();
    }

    public function shouldTrack(Request $request, Response $response): bool
    {
        if (! config('analytics.enabled') || ! $request->isMethod('GET') || $request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($response->getStatusCode() !== 200 || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        if ($request->is(...self::IGNORED_PATHS) || $request->header('Purpose') === 'prefetch' || $request->header('Sec-Purpose') === 'prefetch') {
            return false;
        }

        if (config('analytics.exclude_admins') && $request->user()?->isAdmin()) {
            return false;
        }

        return ! $this->parser->parse($request->userAgent())['is_bot'];
    }

    public function record(Request $request, string $uuid): void
    {
        $agent = $this->parser->parse($request->userAgent());
        $now = now();
        $country = $this->countryFromHeaders($request);
        $referrerHost = $this->externalReferrerHost($request);

        DB::transaction(function () use ($request, $uuid, $agent, $now, $country, $referrerHost): void {
            $visitor = Visitor::firstOrNew(['uuid' => $uuid]);
            $visitor->fill([
                'user_id' => $request->user()?->id ?? $visitor->user_id,
                'ip' => $request->ip(),
                'device_type' => $agent['device_type'],
                'browser' => $agent['browser'],
                'os' => $agent['os'],
                'last_seen_at' => $now,
            ]);
            $visitor->first_seen_at ??= $now;

            if ($country) {
                $visitor->country_code = $country;
            }

            $visitor->save();

            $visit = $visitor->visits()
                ->where('last_activity_at', '>=', $now->copy()->subMinutes(config('analytics.visit_timeout_minutes')))
                ->latest('last_activity_at')
                ->first();

            if (! $visit) {
                $visit = $visitor->visits()->create([
                    'user_id' => $request->user()?->id,
                    'country_code' => $visitor->country_code,
                    'device_type' => $agent['device_type'],
                    'browser' => $agent['browser'],
                    'os' => $agent['os'],
                    'landing_path' => $this->path($request),
                    'referrer_host' => $referrerHost,
                    'started_at' => $now,
                    'last_activity_at' => $now,
                ]);
                $visitor->increment('visits_count');
            }

            PageView::create([
                'visit_id' => $visit->id,
                'visitor_id' => $visitor->id,
                'path' => $this->path($request),
                'route_name' => $request->route()?->getName(),
                'created_at' => $now,
            ]);

            $visit->forceFill([
                'last_activity_at' => $now,
                'user_id' => $visit->user_id ?? $request->user()?->id,
                'page_views_count' => $visit->page_views_count + 1,
            ])->save();
        });
    }

    private function path(Request $request): string
    {
        return Str::limit('/'.ltrim($request->path(), '/'), 250, '');
    }

    /**
     * Cloudflare adds the visitor's country for free when the site is behind it.
     */
    private function countryFromHeaders(Request $request): ?string
    {
        $code = strtoupper((string) $request->header('CF-IPCountry'));

        return preg_match('/^[A-Z]{2}$/', $code) && ! in_array($code, ['XX', 'T1'], true) ? $code : null;
    }

    private function externalReferrerHost(Request $request): ?string
    {
        $host = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        return $host && $host !== $request->getHost() ? Str::limit(Str::lower($host), 250, '') : null;
    }
}
