<?php

namespace App\Services;

/**
 * Lightweight user-agent parsing: device type, browser, operating system and bot detection.
 */
class UserAgentParser
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|whatsapp|telegram|preview|headless|lighthouse|pingdom|monitor|curl|wget|python|java\/|go-http|okhttp|axios|node-fetch|postman|insomnia|scrapy|httpclient/i';

    /**
     * @return array{device_type: string, browser: string, os: string, is_bot: bool}
     */
    public function parse(?string $userAgent): array
    {
        $userAgent = (string) $userAgent;

        return [
            'device_type' => $this->deviceType($userAgent),
            'browser' => $this->browser($userAgent),
            'os' => $this->operatingSystem($userAgent),
            'is_bot' => $userAgent === '' || (bool) preg_match(self::BOT_PATTERN, $userAgent),
        ];
    }

    private function deviceType(string $ua): string
    {
        if (preg_match('/iPad|Tablet|Tab\b|SM-T|SM-X|Kindle|Silk|PlayBook|(Android(?!.*Mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobi|iPhone|iPod|Android.*Mobile|Windows Phone|BlackBerry|Opera Mini|IEMobile/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function browser(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/Edg(e|A|iOS)?\//', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/', $ua) => 'Opera',
            (bool) preg_match('/SamsungBrowser/', $ua) => 'Samsung Internet',
            (bool) preg_match('/UCBrowser/', $ua) => 'UC Browser',
            (bool) preg_match('/YaBrowser/', $ua) => 'Yandex',
            (bool) preg_match('/Firefox|FxiOS/', $ua) => 'Firefox',
            (bool) preg_match('/Chrome|CriOS/', $ua) => 'Chrome',
            (bool) preg_match('/Safari/', $ua) => 'Safari',
            (bool) preg_match('/MSIE|Trident/', $ua) => 'Internet Explorer',
            default => 'Other',
        };
    }

    private function operatingSystem(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/Windows NT/', $ua) => 'Windows',
            (bool) preg_match('/iPhone|iPad|iPod/', $ua) => 'iOS',
            (bool) preg_match('/Android/', $ua) => 'Android',
            (bool) preg_match('/Mac OS X|Macintosh/', $ua) => 'macOS',
            (bool) preg_match('/CrOS/', $ua) => 'Chrome OS',
            (bool) preg_match('/Linux/', $ua) => 'Linux',
            default => 'Other',
        };
    }
}
