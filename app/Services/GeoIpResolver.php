<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Fills in visitor countries from their IP address, in batches, when the admin views analytics.
 */
class GeoIpResolver
{
    /**
     * Code used for localhost / private network addresses (e.g. while testing on XAMPP).
     */
    public const LOCAL = 'ZZ';

    /**
     * Resolve up to $limit visitors whose country is still unknown.
     */
    public function resolvePending(int $limit = 100): int
    {
        $pendingIps = Visitor::whereNull('country_code')->whereNotNull('ip')->distinct()->limit($limit)->pluck('ip');

        if ($pendingIps->isEmpty()) {
            return 0;
        }

        [$privateIps, $publicIps] = $pendingIps->partition(fn (string $ip): bool => ! filter_var(
            $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ));

        foreach ($privateIps as $ip) {
            $this->assign($ip, self::LOCAL, 'Local network');
        }

        if ($publicIps->isEmpty() || ! config('analytics.geoip.enabled')) {
            return $privateIps->count();
        }

        try {
            $results = Http::timeout(config('analytics.geoip.timeout'))
                ->acceptJson()
                ->post(config('analytics.geoip.batch_url'), $publicIps->values()->all())
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            report($exception);

            return $privateIps->count();
        }

        $resolved = 0;
        foreach ((array) $results as $result) {
            if (($result['status'] ?? null) === 'success' && preg_match('/^[A-Z]{2}$/', $result['countryCode'] ?? '')) {
                $this->assign($result['query'], $result['countryCode'], $result['country'] ?? null);
                $resolved++;
            }
        }

        return $privateIps->count() + $resolved;
    }

    private function assign(string $ip, string $code, ?string $name): void
    {
        $visitorIds = Visitor::where('ip', $ip)->whereNull('country_code')->pluck('id');

        Visitor::whereKey($visitorIds)->update(['country_code' => $code, 'country_name' => $name]);
        Visit::whereIn('visitor_id', $visitorIds)->whereNull('country_code')->update(['country_code' => $code]);
    }
}
