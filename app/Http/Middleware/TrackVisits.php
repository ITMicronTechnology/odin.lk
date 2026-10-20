<?php

namespace App\Http\Middleware;

use App\Services\VisitorTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackVisits
{
    public function __construct(private VisitorTracker $tracker) {}

    /**
     * Give each browser an anonymous visitor cookie and mark storefront pages for tracking.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->tracker->shouldTrack($request, $response)) {
            $uuid = $this->tracker->visitorUuid($request);
            $request->attributes->set('analytics.visitor_uuid', $uuid);
            $response->headers->setCookie(cookie(
                config('analytics.cookie'),
                $uuid,
                config('analytics.cookie_days') * 24 * 60,
                sameSite: 'lax',
            ));
        }

        return $response;
    }

    /**
     * Record the page view after the response has been sent, so shoppers never wait for it.
     */
    public function terminate(Request $request, Response $response): void
    {
        $uuid = $request->attributes->get('analytics.visitor_uuid');

        if (! $uuid) {
            return;
        }

        try {
            $this->tracker->record($request, $uuid);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
