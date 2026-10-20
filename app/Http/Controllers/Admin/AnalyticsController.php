<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeoIpResolver;
use App\Services\VisitorStats;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private VisitorStats $stats) {}

    public function index(Request $request, GeoIpResolver $geoIp): View
    {
        $range = array_key_exists($request->query('range'), VisitorStats::RANGES) ? $request->query('range') : '30d';
        $since = $this->stats->since($range);

        // Look up countries for any new visitors before building the report.
        $geoIp->resolvePending();

        return view('admin.analytics.index', [
            'range' => $range,
            'ranges' => VisitorStats::RANGES,
            'summary' => $this->stats->summary($since),
            'daily' => $this->stats->daily($since),
            'countries' => $this->stats->countries($since),
            'devices' => $this->stats->breakdown('device_type', $since, 5),
            'browsers' => $this->stats->breakdown('browser', $since),
            'systems' => $this->stats->breakdown('os', $since),
            'referrers' => $this->stats->breakdown('referrer_host', $since),
            'pages' => $this->stats->topPages($since),
            'online' => $this->stats->onlineVisits(),
            'recent' => $this->stats->recentVisits(),
            'stats' => $this->stats,
        ]);
    }

    /**
     * Live numbers for the auto-refreshing "online now" widgets.
     */
    public function live(): JsonResponse
    {
        return response()->json([
            'online' => $this->stats->onlineCount(),
            'today' => $this->stats->activeVisitorsSince(today()),
            'visits' => $this->stats->onlineVisits()->map(fn ($visit): array => [
                'flag' => $this->stats->flag($visit->country_code),
                'country' => $this->stats->countryName($visit->country_code),
                'device' => $visit->device_type,
                'browser' => $visit->browser,
                'path' => $visit->current_path ?? $visit->landing_path,
                'seen' => $visit->last_activity_at->diffForHumans(),
            ]),
        ]);
    }
}
