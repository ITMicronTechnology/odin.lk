<?php

namespace Tests\Unit;

use App\Services\UserAgentParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserAgentParserTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string, 4: bool}>
     */
    public static function userAgents(): array
    {
        return [
            'Chrome on Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', false],
            'Edge on Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36 Edg/126.0', 'desktop', 'Edge', 'Windows', false],
            'Safari on iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1', 'mobile', 'Safari', 'iOS', false],
            'Samsung Internet on Android' => ['Mozilla/5.0 (Linux; Android 14; SM-S928B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/25.0 Chrome/121.0 Mobile Safari/537.36', 'mobile', 'Samsung Internet', 'Android', false],
            'Android tablet' => ['Mozilla/5.0 (Linux; Android 13; SM-X710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36', 'tablet', 'Chrome', 'Android', false],
            'Firefox on Mac' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14.5; rv:127.0) Gecko/20100101 Firefox/127.0', 'desktop', 'Firefox', 'macOS', false],
            'Googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'desktop', 'Other', 'Other', true],
            'curl' => ['curl/8.4.0', 'desktop', 'Other', 'Other', true],
        ];
    }

    #[DataProvider('userAgents')]
    public function test_it_detects_device_browser_os_and_bots(string $userAgent, string $device, string $browser, string $os, bool $isBot): void
    {
        $result = (new UserAgentParser)->parse($userAgent);

        $this->assertSame($device, $result['device_type']);
        $this->assertSame($browser, $result['browser']);
        $this->assertSame($os, $result['os']);
        $this->assertSame($isBot, $result['is_bot']);
    }

    public function test_missing_user_agent_is_treated_as_a_bot(): void
    {
        $this->assertTrue((new UserAgentParser)->parse(null)['is_bot']);
    }
}
