<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;

/**
 * Draws simple vector "sample" images so the store looks complete before real photos are uploaded.
 */
class SampleImageFactory
{
    private const BG_TINTS = ['#e9eef5', '#f3e9ea', '#e8edf7'];

    /**
     * Write a product illustration to the public disk and return its relative path.
     */
    public function product(string $path, string $device, string $accent, string $brand = '', int $variant = 0): string
    {
        $tint = self::BG_TINTS[$variant % count(self::BG_TINTS)];
        $transform = match ($variant % 3) {
            1 => 'translate(400 420) scale(0.86) translate(-400 -420)',
            2 => 'rotate(-7 400 420)',
            default => '',
        };

        $brandLabel = $brand !== ''
            ? '<text x="48" y="762" font-family="Arial Black, Arial, sans-serif" font-size="30" font-weight="900" fill="#0b0b0f" opacity=".82" letter-spacing="2">'.$this->e(mb_strtoupper($brand)).'</text>'
            : '';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="800" height="800">
  {$this->defs($accent, $tint)}
  <rect width="800" height="800" fill="url(#bg)"/>
  <ellipse cx="400" cy="655" rx="270" ry="26" fill="#0b0b0f" opacity=".10"/>
  <g transform="{$transform}">{$this->device($device, $accent)}</g>
  {$brandLabel}
  <text x="752" y="760" text-anchor="end" font-family="Arial, sans-serif" font-size="18" fill="#8a929c" letter-spacing="1">SAMPLE IMAGE · odin.lk</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * Round category thumbnail.
     */
    public function category(string $path, string $device, string $accent): string
    {
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="400" height="400">
  {$this->defs($accent, '#eef2f8')}
  <rect width="800" height="800" fill="url(#bg)"/>
  <ellipse cx="400" cy="655" rx="250" ry="24" fill="#0b0b0f" opacity=".10"/>
  <g transform="translate(400 420) scale(0.92) translate(-400 -420)">{$this->device($device, $accent)}</g>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * Text-based brand logo.
     */
    public function brandLogo(string $path, string $name, string $color): string
    {
        $name = $this->e($name);
        $size = mb_strlen($name) > 8 ? 44 : 56;

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 140" width="320" height="140">
  <rect width="320" height="140" rx="16" fill="#ffffff"/>
  <text x="160" y="88" text-anchor="middle" font-family="Arial Black, Arial, sans-serif" font-size="{$size}" font-weight="900" fill="{$color}" letter-spacing="1">{$name}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * Promotional banner for the home page.
     *
     * @param  'slider'|'side'|'wide'  $position
     */
    public function banner(string $path, string $position, string $title, string $subtitle, string $device, string $accent, string $theme = 'dark'): string
    {
        [$width, $height] = match ($position) {
            'side' => [800, 300],
            'wide' => [1600, 300],
            default => [1600, 520],
        };

        [$from, $to] = match ($theme) {
            'red' => ['#7a0008', '#e10600'],
            'blue' => ['#061a44', '#0a3d91'],
            default => ['#05070d', '#0a3d91'],
        };

        // Shrink long slider titles so they never run into the device drawing on the right.
        $titleSize = (int) min(78, floor(780 / (0.72 * max(1, mb_strlen($title)))));
        $title = $this->e($title);
        $subtitle = $this->e($subtitle);

        $content = match ($position) {
            'side' => <<<SVG
  <text x="48" y="100" font-family="Arial Black, Arial, sans-serif" font-size="40" font-weight="900" fill="#fff">{$title}</text>
  <text x="48" y="145" font-family="Arial, sans-serif" font-size="26" fill="#fff" opacity=".85">{$subtitle}</text>
  <rect x="48" y="185" width="190" height="56" rx="28" fill="#fff"/>
  <text x="143" y="221" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="22" fill="#0b0b0f">SHOP NOW</text>
  <g transform="translate(500 -6) scale(0.40)">{$this->device($device, $accent)}</g>
SVG,
            'wide' => <<<SVG
  <text x="80" y="135" font-family="Arial Black, Arial, sans-serif" font-size="64" font-weight="900" fill="#fff">{$title}</text>
  <text x="80" y="190" font-family="Arial, sans-serif" font-size="30" fill="#fff" opacity=".85">{$subtitle}</text>
  <g transform="translate(1080 -40) scale(0.46)">{$this->device($device, $accent)}</g>
  <g transform="translate(1290 -20) scale(0.40)">{$this->device('speaker', '#e10600')}</g>
SVG,
            default => <<<SVG
  <rect x="80" y="110" width="170" height="40" rx="20" fill="#e10600"/>
  <text x="165" y="138" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="20" fill="#fff" letter-spacing="2">ODIN.LK</text>
  <text x="80" y="240" font-family="Arial Black, Arial, sans-serif" font-size="{$titleSize}" font-weight="900" fill="#fff">{$title}</text>
  <text x="80" y="300" font-family="Arial, sans-serif" font-size="34" fill="#fff" opacity=".85">{$subtitle}</text>
  <rect x="80" y="350" width="230" height="64" rx="32" fill="#e10600"/>
  <text x="195" y="391" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="24" fill="#fff" letter-spacing="1">SHOP NOW</text>
  <circle cx="1210" cy="260" r="250" fill="#fff" opacity=".06"/>
  <circle cx="1210" cy="260" r="180" fill="#fff" opacity=".06"/>
  <g transform="translate(900 -20) scale(0.72)">{$this->device($device, $accent)}</g>
SVG,
        };

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} {$height}" width="{$width}" height="{$height}">
  {$this->defs($accent, '#ffffff')}
  <linearGradient id="banner" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$from}"/><stop offset="1" stop-color="{$to}"/></linearGradient>
  <rect width="{$width}" height="{$height}" fill="url(#banner)"/>
  <path d="M{$width} 0 L{$width} {$height} L{$this->half($width)} {$height} Z" fill="#fff" opacity=".04"/>
  <rect x="0" y="{$this->minus($height, 10)}" width="{$width}" height="10" fill="#e10600"/>
{$content}
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private function defs(string $accent, string $tint): string
    {
        $accentDark = $this->shade($accent, -0.45);

        return <<<SVG
<defs>
    <radialGradient id="bg" cx="50%" cy="42%" r="75%"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="{$tint}"/></radialGradient>
    <linearGradient id="accent" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$accent}"/><stop offset="1" stop-color="{$accentDark}"/></linearGradient>
    <linearGradient id="metal" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#eef1f5"/><stop offset="1" stop-color="#b6bec9"/></linearGradient>
    <linearGradient id="light" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#dfe4ea"/></linearGradient>
    <linearGradient id="dark" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b404c"/><stop offset="1" stop-color="#14161b"/></linearGradient>
    <linearGradient id="screen" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$accent}"/><stop offset=".55" stop-color="#0b1a3a"/><stop offset="1" stop-color="#050608"/></linearGradient>
    <pattern id="dots" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="7" cy="7" r="2.6" fill="#000" opacity=".35"/></pattern>
  </defs>
SVG;
    }

    private function device(string $device, string $a): string
    {
        $wave = '<path d="M0 0" fill="none"/>';

        return match ($device) {
            'laptop' => <<<SVG
<rect x="170" y="170" width="460" height="310" rx="16" fill="url(#dark)"/>
<rect x="186" y="186" width="428" height="272" rx="4" fill="url(#screen)"/>
<path d="M186 400 C 300 330, 420 470, 614 360 L614 458 L186 458 Z" fill="#fff" opacity=".08"/>
<path d="M186 310 C 320 250, 440 390, 614 290" stroke="{$a}" stroke-width="4" fill="none" opacity=".75"/>
<circle cx="400" cy="178" r="3" fill="#6b7280"/>
<path d="M110 482 H690 L660 532 Q654 542 640 542 H160 Q146 542 140 532 Z" fill="url(#metal)"/>
<rect x="340" y="482" width="120" height="10" rx="5" fill="#9aa3ae"/>
SVG,
            'desktop' => <<<SVG
<rect x="90" y="200" width="420" height="270" rx="12" fill="url(#dark)"/>
<rect x="104" y="214" width="392" height="232" rx="3" fill="url(#screen)"/>
<path d="M104 380 C 220 320, 330 430, 496 350" stroke="{$a}" stroke-width="4" fill="none" opacity=".75"/>
<path d="M270 470 h60 l14 88 h-88 z" fill="url(#metal)"/>
<rect x="220" y="556" width="160" height="14" rx="7" fill="url(#metal)"/>
<rect x="545" y="190" width="170" height="400" rx="14" fill="url(#dark)"/>
<rect x="562" y="212" width="136" height="280" rx="6" fill="#0b0d12" stroke="{$a}" stroke-width="3"/>
<circle cx="630" cy="285" r="40" fill="none" stroke="{$a}" stroke-width="5"/>
<circle cx="630" cy="285" r="10" fill="{$a}"/>
<circle cx="630" cy="400" r="40" fill="none" stroke="{$a}" stroke-width="5"/>
<circle cx="630" cy="400" r="10" fill="{$a}"/>
<rect x="580" y="525" width="100" height="6" rx="3" fill="{$a}"/>
<circle cx="630" cy="560" r="9" fill="#9aa3ae"/>
SVG,
            'aio' => <<<SVG
<rect x="110" y="160" width="580" height="380" rx="16" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<rect x="126" y="176" width="548" height="310" rx="4" fill="url(#screen)"/>
<path d="M126 420 C 260 350, 420 480, 674 380" stroke="{$a}" stroke-width="4" fill="none" opacity=".75"/>
<circle cx="400" cy="514" r="8" fill="#9aa3ae"/>
<path d="M360 540 h80 l40 70 h-160 z" fill="url(#metal)"/>
SVG,
            'monitor' => <<<SVG
<rect x="110" y="165" width="580" height="350" rx="14" fill="url(#dark)"/>
<rect x="124" y="179" width="552" height="310" rx="3" fill="url(#screen)"/>
<path d="M124 420 C 260 340, 420 490, 676 370" stroke="{$a}" stroke-width="5" fill="none" opacity=".75"/>
<path d="M124 470 C 300 400, 440 520, 676 440 L676 489 L124 489 Z" fill="#fff" opacity=".06"/>
<rect x="380" y="498" width="40" height="8" rx="4" fill="{$a}"/>
<path d="M370 515 h60 l20 90 h-100 z" fill="url(#metal)"/>
<ellipse cx="400" cy="606" rx="130" ry="16" fill="url(#metal)"/>
SVG,
            'printer', 'laser' => $this->printer($a, $device === 'laser'),
            'ink' => $this->inkBottles(),
            'keyboard' => $this->keyboard($a),
            'mouse' => <<<SVG
<path d="M400 160 C 505 160 545 245 545 340 V 480 C 545 585 482 645 400 645 C 318 645 255 585 255 480 V 340 C 255 245 295 160 400 160 Z" fill="url(#dark)"/>
<path d="M400 160 V 330" stroke="#0b0d12" stroke-width="5"/>
<path d="M258 330 H 542" stroke="#0b0d12" stroke-width="3" opacity=".6"/>
<rect x="386" y="210" width="28" height="70" rx="14" fill="{$a}"/>
<path d="M268 450 C 300 540 500 540 532 450" stroke="{$a}" stroke-width="9" fill="none" opacity=".9"/>
<rect x="244" y="360" width="18" height="50" rx="8" fill="#2a2e36"/>
<rect x="244" y="420" width="18" height="40" rx="8" fill="#2a2e36"/>
SVG,
            'webcam' => <<<SVG
<rect x="200" y="280" width="400" height="170" rx="85" fill="url(#dark)"/>
<circle cx="400" cy="365" r="62" fill="#0b0d12" stroke="#3a3f4b" stroke-width="12"/>
<circle cx="400" cy="365" r="26" fill="{$a}" opacity=".75"/>
<circle cx="388" cy="352" r="8" fill="#fff" opacity=".6"/>
<circle cx="520" cy="365" r="7" fill="{$a}"/>
<path d="M340 450 h120 l34 150 h-188 z" fill="#2a2e36"/>
SVG,
            'cctv_bullet' => <<<SVG
<rect x="575" y="190" width="100" height="170" rx="16" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<path d="M620 285 L 520 380" stroke="#c8ced6" stroke-width="34" stroke-linecap="round"/>
<g transform="rotate(-14 400 400)">
  <rect x="160" y="340" width="400" height="150" rx="40" fill="url(#light)" stroke="#c8ced6" stroke-width="4"/>
  <path d="M150 322 H548 Q572 322 572 346 V366 H140 V338 Q140 322 150 322 Z" fill="#e4e8ee" stroke="#c8ced6" stroke-width="3"/>
  <rect x="140" y="356" width="46" height="120" rx="16" fill="#111827"/>
  <circle cx="163" cy="416" r="32" fill="#0b0d12" stroke="{$a}" stroke-width="6"/>
  <circle cx="155" cy="408" r="9" fill="#fff" opacity=".45"/>
  <rect x="250" y="470" width="220" height="10" rx="5" fill="{$a}" opacity=".85"/>
</g>
SVG,
            'cctv_dome' => <<<SVG
<ellipse cx="400" cy="270" rx="240" ry="46" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<rect x="160" y="270" width="480" height="40" fill="url(#light)"/>
<path d="M160 310 H640" stroke="#c8ced6" stroke-width="3"/>
<path d="M190 310 A 210 210 0 0 0 610 310 Z" fill="#111827" opacity=".94"/>
<path d="M240 340 A 170 170 0 0 0 330 480" stroke="#fff" stroke-width="10" fill="none" opacity=".18" stroke-linecap="round"/>
<circle cx="400" cy="410" r="58" fill="#0b0d12" stroke="{$a}" stroke-width="7"/>
<circle cx="386" cy="396" r="14" fill="#fff" opacity=".4"/>
SVG,
            'cctv_kit' => <<<SVG
<rect x="150" y="430" width="500" height="120" rx="14" fill="url(#dark)"/>
<rect x="180" y="470" width="160" height="12" rx="6" fill="{$a}"/>
<circle cx="560" cy="490" r="10" fill="#22c55e"/><circle cx="596" cy="490" r="10" fill="{$a}"/>
<g transform="translate(40 -60) scale(0.55)"><rect x="160" y="340" width="400" height="150" rx="40" fill="url(#light)" stroke="#c8ced6" stroke-width="6"/><rect x="140" y="356" width="46" height="120" rx="16" fill="#111827"/><circle cx="163" cy="416" r="32" fill="#0b0d12" stroke="{$a}" stroke-width="8"/></g>
<g transform="translate(330 -60) scale(0.55)"><rect x="160" y="340" width="400" height="150" rx="40" fill="url(#light)" stroke="#c8ced6" stroke-width="6"/><rect x="140" y="356" width="46" height="120" rx="16" fill="#111827"/><circle cx="163" cy="416" r="32" fill="#0b0d12" stroke="{$a}" stroke-width="8"/></g>
<g transform="translate(90 150) scale(0.45)"><path d="M190 310 A 210 210 0 0 0 610 310 Z" fill="#111827"/><rect x="170" y="280" width="460" height="34" rx="10" fill="url(#light)"/><circle cx="400" cy="410" r="58" fill="#0b0d12" stroke="{$a}" stroke-width="9"/></g>
<g transform="translate(350 150) scale(0.45)"><path d="M190 310 A 210 210 0 0 0 610 310 Z" fill="#111827"/><rect x="170" y="280" width="460" height="34" rx="10" fill="url(#light)"/><circle cx="400" cy="410" r="58" fill="#0b0d12" stroke="{$a}" stroke-width="9"/></g>
SVG,
            'wificam' => <<<SVG
<rect x="300" y="480" width="200" height="130" rx="34" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<circle cx="400" cy="350" r="155" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<rect x="285" y="295" width="230" height="116" rx="58" fill="#0b0d12"/>
<circle cx="400" cy="353" r="38" fill="#1f2937" stroke="{$a}" stroke-width="7"/>
<circle cx="400" cy="353" r="13" fill="#60a5fa" opacity=".7"/>
<circle cx="470" cy="330" r="6" fill="#22c55e"/>
SVG,
            'powerbank' => <<<SVG
<path d="M430 182 C 430 100 610 110 610 220 V 430" stroke="#e5e7eb" stroke-width="14" fill="none" stroke-linecap="round"/>
<rect x="594" y="420" width="32" height="70" rx="8" fill="#9aa3ae"/>
<rect x="260" y="180" width="280" height="450" rx="38" fill="url(#dark)"/>
<rect x="260" y="180" width="280" height="450" rx="38" fill="url(#accent)" opacity=".22"/>
<rect x="320" y="240" width="160" height="80" rx="12" fill="#0b0d12"/>
<text x="400" y="294" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="38" fill="{$a}">100%</text>
<circle cx="340" cy="570" r="9" fill="{$a}"/><circle cx="373" cy="570" r="9" fill="{$a}"/><circle cx="406" cy="570" r="9" fill="{$a}"/><circle cx="439" cy="570" r="9" fill="#4b5563"/>
<rect x="320" y="172" width="44" height="12" rx="4" fill="#111"/><rect x="410" y="172" width="40" height="12" rx="4" fill="#111"/>
SVG,
            'charger' => <<<'SVG'
<rect x="345" y="190" width="24" height="80" rx="6" fill="url(#metal)"/>
<rect x="431" y="190" width="24" height="80" rx="6" fill="url(#metal)"/>
<rect x="270" y="250" width="260" height="270" rx="44" fill="url(#light)" stroke="#c8ced6" stroke-width="4"/>
<circle cx="400" cy="345" r="36" fill="url(#accent)"/>
<path d="M392 322 l-14 28 h20 l-8 26 26 -34 h-20 l10 -20 z" fill="#fff"/>
<rect x="345" y="430" width="110" height="24" rx="10" fill="#1f2937"/>
<rect x="355" y="468" width="90" height="20" rx="8" fill="#1f2937"/>
<path d="M400 520 C 400 610 560 600 600 520" stroke="#1f2937" stroke-width="12" fill="none" stroke-linecap="round"/>
<rect x="585" y="470" width="34" height="60" rx="8" fill="#9aa3ae"/>
SVG,
            'cable' => <<<'SVG'
<path d="M200 230 C 120 360 280 420 400 400 C 540 380 700 440 600 570" stroke="#1f2937" stroke-width="16" fill="none" stroke-linecap="round"/>
<rect x="176" y="170" width="48" height="80" rx="10" fill="url(#accent)"/>
<rect x="188" y="140" width="24" height="34" rx="6" fill="url(#metal)"/>
<rect x="576" y="560" width="48" height="80" rx="10" fill="url(#accent)"/>
<rect x="588" y="636" width="24" height="30" rx="6" fill="url(#metal)"/>
SVG,
            'speaker' => <<<'SVG'
<path d="M180 300 C 120 250 150 190 210 225" stroke="#222" stroke-width="14" fill="none" stroke-linecap="round"/>
<rect x="130" y="290" width="540" height="260" rx="70" fill="url(#dark)"/>
<rect x="160" y="300" width="480" height="240" rx="40" fill="url(#dots)"/>
<rect x="130" y="290" width="54" height="260" rx="27" fill="url(#accent)"/>
<rect x="616" y="290" width="54" height="260" rx="27" fill="url(#accent)"/>
<rect x="340" y="400" width="120" height="40" rx="10" fill="#fff" opacity=".14"/>
<rect x="320" y="274" width="40" height="18" rx="6" fill="#2a2e36"/><rect x="380" y="274" width="40" height="18" rx="6" fill="#2a2e36"/><rect x="440" y="274" width="40" height="18" rx="6" fill="#2a2e36"/>
SVG,
            'speaker_small' => <<<'SVG'
<rect x="250" y="250" width="300" height="340" rx="80" fill="url(#accent)"/>
<rect x="275" y="275" width="250" height="250" rx="60" fill="url(#dots)"/>
<circle cx="400" cy="400" r="70" fill="#000" opacity=".25"/>
<rect x="350" y="545" width="100" height="18" rx="9" fill="#fff" opacity=".25"/>
<path d="M270 300 C 200 260 210 200 270 210" stroke="#111" stroke-width="12" fill="none" stroke-linecap="round"/>
SVG,
            'headphones' => <<<SVG
<path d="M225 440 V 360 C 225 225 575 225 575 360 V 440" fill="none" stroke="#1e2128" stroke-width="36" stroke-linecap="round"/>
<path d="M245 360 C 250 260 550 260 555 360" fill="none" stroke="{$a}" stroke-width="6" opacity=".8"/>
<rect x="165" y="400" width="125" height="200" rx="58" fill="url(#dark)"/>
<rect x="510" y="400" width="125" height="200" rx="58" fill="url(#dark)"/>
<rect x="188" y="428" width="80" height="144" rx="40" fill="none" stroke="{$a}" stroke-width="6"/>
<rect x="533" y="428" width="80" height="144" rx="40" fill="none" stroke="{$a}" stroke-width="6"/>
SVG,
            'earbuds' => <<<SVG
<rect x="245" y="340" width="310" height="240" rx="115" fill="url(#dark)"/>
<path d="M248 432 H552" stroke="#000" opacity=".45" stroke-width="4"/>
<circle cx="400" cy="520" r="7" fill="{$a}"/>
<g transform="translate(250 170)"><rect x="28" y="60" width="36" height="110" rx="18" fill="url(#dark)"/><circle cx="46" cy="50" r="50" fill="url(#dark)"/><circle cx="46" cy="50" r="20" fill="{$a}"/></g>
<g transform="translate(460 170)"><rect x="28" y="60" width="36" height="110" rx="18" fill="url(#dark)"/><circle cx="46" cy="50" r="50" fill="url(#dark)"/><circle cx="46" cy="50" r="20" fill="{$a}"/></g>
SVG,
            'router' => <<<SVG
<rect x="190" y="170" width="22" height="260" rx="10" fill="#1e2128" transform="rotate(-12 200 430)"/>
<rect x="320" y="150" width="22" height="280" rx="10" fill="#1e2128"/>
<rect x="458" y="150" width="22" height="280" rx="10" fill="#1e2128"/>
<rect x="588" y="170" width="22" height="260" rx="10" fill="#1e2128" transform="rotate(12 600 430)"/>
<path d="M170 400 H630 L680 440 H120 Z" fill="#3b404c"/>
<path d="M120 440 H680 L640 550 H160 Z" fill="url(#dark)"/>
<circle cx="260" cy="495" r="8" fill="{$a}"/><circle cx="300" cy="495" r="8" fill="{$a}"/><circle cx="340" cy="495" r="8" fill="{$a}"/><circle cx="380" cy="495" r="8" fill="#22c55e"/><circle cx="420" cy="495" r="8" fill="#22c55e"/>
<rect x="470" y="485" width="120" height="16" rx="8" fill="{$a}" opacity=".7"/>
SVG,
            'mesh' => <<<SVG
<rect x="170" y="230" width="210" height="370" rx="60" fill="url(#light)" stroke="#c8ced6" stroke-width="4"/>
<rect x="420" y="230" width="210" height="370" rx="60" fill="url(#light)" stroke="#c8ced6" stroke-width="4"/>
<circle cx="275" cy="300" r="9" fill="{$a}"/><circle cx="525" cy="300" r="9" fill="{$a}"/>
<path d="M235 470 q40 -40 80 0 M250 490 q25 -25 50 0" stroke="{$a}" stroke-width="6" fill="none" stroke-linecap="round"/>
<path d="M485 470 q40 -40 80 0 M500 490 q25 -25 50 0" stroke="{$a}" stroke-width="6" fill="none" stroke-linecap="round"/>
SVG,
            'switch' => $this->networkSwitch($a),
            'ssd' => $this->nvme($a),
            'ssd_sata' => <<<'SVG'
<rect x="190" y="220" width="420" height="330" rx="18" fill="url(#dark)"/>
<rect x="220" y="250" width="360" height="200" rx="10" fill="url(#accent)"/>
<text x="400" y="365" text-anchor="middle" font-family="Arial Black, Arial, sans-serif" font-size="56" font-weight="900" fill="#fff">SSD</text>
<rect x="220" y="480" width="200" height="14" rx="7" fill="#4b5563"/>
<rect x="220" y="506" width="140" height="14" rx="7" fill="#4b5563"/>
SVG,
            'hdd' => <<<'SVG'
<rect x="250" y="180" width="300" height="440" rx="34" fill="url(#accent)"/>
<rect x="250" y="180" width="300" height="440" rx="34" fill="#000" opacity=".15"/>
<path d="M250 260 H550" stroke="#fff" stroke-width="3" opacity=".25"/>
<circle cx="400" cy="560" r="8" fill="#fff"/>
<rect x="340" y="610" width="120" height="16" rx="6" fill="#1f2937"/>
SVG,
            'flash' => <<<'SVG'
<g transform="rotate(-30 400 400)">
  <rect x="250" y="340" width="260" height="120" rx="26" fill="url(#accent)"/>
  <rect x="505" y="360" width="120" height="80" rx="6" fill="url(#metal)"/>
  <rect x="540" y="380" width="22" height="16" fill="#6b7280"/><rect x="580" y="380" width="22" height="16" fill="#6b7280"/>
  <circle cx="290" cy="400" r="16" fill="none" stroke="#fff" stroke-width="6" opacity=".7"/>
</g>
SVG,
            'phone' => $this->smartphone($a, 'square'),
            'phone_s' => $this->smartphone($a, 'vertical'),
            'tablet' => <<<SVG
<g transform="rotate(-6 400 420)">
  <rect x="120" y="205" width="560" height="390" rx="32" fill="#1b1d22"/>
  <rect x="137" y="222" width="526" height="356" rx="18" fill="url(#screen)"/>
  <path d="M137 470 C 260 400, 420 540, 663 430 L663 560 Q663 578 645 578 L155 578 Q137 578 137 560 Z" fill="#fff" opacity=".07"/>
  <path d="M137 400 C 280 330, 430 470, 663 360" stroke="{$a}" stroke-width="5" fill="none" opacity=".8"/>
  <circle cx="400" cy="213" r="4" fill="#4b5563"/>
  <rect x="682" y="300" width="5" height="60" rx="2" fill="#2a2e36"/>
</g>
SVG,
            'smartwatch' => <<<SVG
<path d="M335 130 H465 L452 275 H348 Z" fill="#1f2937"/>
<path d="M348 525 H452 L465 670 H335 Z" fill="#1f2937"/>
<path d="M350 170 H450 M352 210 H448 M348 590 H452 M346 630 H454" stroke="#374151" stroke-width="6"/>
<rect x="280" y="255" width="240" height="290" rx="70" fill="url(#metal)"/>
<rect x="298" y="273" width="204" height="254" rx="56" fill="#050608"/>
<circle cx="400" cy="400" r="86" stroke="#1f2937" stroke-width="12" fill="none"/>
<circle cx="400" cy="400" r="86" stroke="{$a}" stroke-width="12" fill="none" stroke-dasharray="360 600" stroke-linecap="round" transform="rotate(-90 400 400)"/>
<text x="400" y="418" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="50" fill="#fff">10:09</text>
<text x="400" y="452" text-anchor="middle" font-family="Arial, sans-serif" font-size="20" fill="#9ca3af">8,450 steps</text>
<rect x="518" y="360" width="18" height="46" rx="7" fill="#9aa3ae"/>
<rect x="518" y="420" width="12" height="30" rx="5" fill="#9aa3ae"/>
SVG,
            'feature_phone' => $this->featurePhone($a),
            default => $wave,
        };
    }

    /**
     * Smartphone shown from the back (tilted, behind) and the front.
     *
     * @param  'square'|'vertical'  $cameraStyle
     */
    private function smartphone(string $a, string $cameraStyle): string
    {
        $lens = fn (int $x, int $y): string => "<circle cx=\"{$x}\" cy=\"{$y}\" r=\"23\" fill=\"#0b0d12\" stroke=\"#3b404c\" stroke-width=\"6\"/><circle cx=\"".($x - 6).'" cy="'.($y - 6).'" r="6" fill="#3b5b8f" opacity=".8"/>';

        $cameras = $cameraStyle === 'square'
            ? '<rect x="218" y="170" width="128" height="128" rx="32" fill="#000" opacity=".25"/>'.$lens(252, 204).$lens(252, 264).$lens(310, 234).'<circle cx="312" cy="190" r="8" fill="#f3f4f6"/>'
            : $lens(250, 206).$lens(250, 266).$lens(250, 326).'<circle cx="296" cy="206" r="8" fill="#f3f4f6"/>';

        $frontCamera = $cameraStyle === 'square'
            ? '<rect x="452" y="190" width="66" height="18" rx="9" fill="#050608"/>'
            : '<circle cx="485" cy="199" r="9" fill="#050608"/>';

        return <<<SVG
<g transform="rotate(-12 330 400)">
  <rect x="195" y="145" width="260" height="510" rx="44" fill="url(#accent)"/>
  <rect x="195" y="145" width="260" height="510" rx="44" fill="#fff" opacity=".1"/>
  <path d="M195 420 L455 300 L455 360 L195 480 Z" fill="#fff" opacity=".06"/>
  {$cameras}
</g>
<rect x="355" y="160" width="260" height="510" rx="44" fill="#15171c"/>
<rect x="367" y="172" width="236" height="486" rx="34" fill="url(#screen)"/>
<path d="M367 480 C 430 425, 520 530, 603 455 L603 624 Q603 658 569 658 L401 658 Q367 658 367 624 Z" fill="#fff" opacity=".08"/>
<path d="M367 385 C 440 335, 520 440, 603 365" stroke="{$a}" stroke-width="5" fill="none" opacity=".85"/>
{$frontCamera}
<rect x="615" y="270" width="6" height="64" rx="3" fill="#2a2e36"/>
<rect x="615" y="350" width="6" height="40" rx="3" fill="#2a2e36"/>
SVG;
    }

    private function featurePhone(string $a): string
    {
        $keys = '';
        foreach (range(0, 3) as $row) {
            foreach (range(0, 2) as $col) {
                $x = 328 + $col * 52;
                $y = 412 + $row * 44;
                $keys .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"40\" height=\"30\" rx=\"9\" fill=\"#2c313b\"/>";
            }
        }

        return <<<SVG
<rect x="295" y="135" width="210" height="530" rx="62" fill="url(#dark)"/>
<rect x="295" y="135" width="210" height="530" rx="62" fill="url(#accent)" opacity=".18"/>
<rect x="370" y="160" width="60" height="8" rx="4" fill="#0b0d12"/>
<rect x="322" y="188" width="156" height="136" rx="10" fill="url(#screen)"/>
<text x="400" y="266" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="34" fill="#fff">12:30</text>
<rect x="348" y="342" width="104" height="48" rx="24" fill="#2a2e36"/>
<circle cx="400" cy="366" r="13" fill="{$a}"/>
<rect x="318" y="356" width="24" height="20" rx="6" fill="#22c55e" opacity=".85"/>
<rect x="458" y="356" width="24" height="20" rx="6" fill="#ef4444" opacity=".85"/>
{$keys}
SVG;
    }

    private function printer(string $a, bool $isLaser): string
    {
        $tanks = $isLaser ? '' : <<<'SVG'
<rect x="170" y="330" width="22" height="70" rx="5" fill="#06b6d4"/><rect x="198" y="330" width="22" height="70" rx="5" fill="#db2777"/><rect x="226" y="330" width="22" height="70" rx="5" fill="#facc15"/><rect x="254" y="330" width="22" height="70" rx="5" fill="#111827"/>
SVG;
        $body = $isLaser ? 'url(#dark)' : 'url(#metal)';
        $top = $isLaser ? '#2a2e36' : '#f4f6f9';

        return <<<SVG
<path d="M260 150 H540 V280 H260 Z" fill="#fff" stroke="#d6dbe1" stroke-width="3"/>
<path d="M290 190 H500 M290 215 H470 M290 240 H490" stroke="#cbd2da" stroke-width="6"/>
<rect x="140" y="250" width="520" height="270" rx="26" fill="{$body}"/>
<rect x="140" y="250" width="520" height="64" rx="26" fill="{$top}"/>
<rect x="470" y="280" width="150" height="46" rx="8" fill="#0b0d12"/>
<rect x="484" y="292" width="70" height="22" rx="4" fill="{$a}" opacity=".85"/>
<circle cx="580" cy="303" r="9" fill="#22c55e"/>
<rect x="140" y="372" width="520" height="10" fill="{$a}"/>
{$tanks}
<rect x="200" y="420" width="400" height="20" rx="10" fill="#1f2328"/>
<path d="M230 432 H570 L600 560 H200 Z" fill="#fff" stroke="#d6dbe1" stroke-width="3"/>
SVG;
    }

    private function inkBottles(): string
    {
        $svg = '';
        foreach (['#06b6d4', '#db2777', '#facc15', '#111827'] as $index => $color) {
            $x = 150 + $index * 130;
            $svg .= <<<SVG
<rect x="{$x}" y="250" width="110" height="340" rx="22" fill="url(#light)" stroke="#c8ced6" stroke-width="3"/>
<rect x="{$x}" y="380" width="110" height="210" rx="22" fill="{$color}"/>
<rect x="{$this->plus($x, 30)}" y="210" width="50" height="46" rx="8" fill="{$color}"/>
SVG;
        }

        return $svg;
    }

    private function keyboard(string $a): string
    {
        $keys = '';
        for ($row = 0; $row < 4; $row++) {
            for ($col = 0; $col < 13; $col++) {
                $x = 110 + $col * 35;
                $y = 350 + $row * 34;
                $keys .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"29\" height=\"27\" rx=\"5\" fill=\"#2c313b\"/>";
            }
        }

        return <<<SVG
<rect x="90" y="330" width="500" height="200" rx="18" fill="url(#dark)"/>
<rect x="90" y="330" width="500" height="200" rx="18" fill="none" stroke="{$a}" stroke-width="3" opacity=".7"/>
{$keys}
<rect x="215" y="488" width="250" height="27" rx="5" fill="#2c313b"/>
<path d="M660 320 C 712 320 730 362 730 412 V 470 C 730 522 700 552 660 552 C 620 552 590 522 590 470 V 412 C 590 362 608 320 660 320 Z" fill="url(#dark)"/>
<path d="M660 320 V 405" stroke="#0b0d12" stroke-width="4"/>
<rect x="652" y="350" width="16" height="36" rx="8" fill="{$a}"/>
SVG;
    }

    private function networkSwitch(string $a): string
    {
        $ports = '';
        for ($i = 0; $i < 8; $i++) {
            $x = 175 + $i * 58;
            $ports .= "<rect x=\"{$x}\" y=\"395\" width=\"42\" height=\"36\" rx=\"4\" fill=\"#0b0d12\"/><circle cx=\"".($x + 21)."\" cy=\"452\" r=\"5\" fill=\"{$a}\"/>";
        }

        return <<<SVG
<path d="M160 330 H640 L680 360 H120 Z" fill="#3b404c"/>
<rect x="120" y="360" width="560" height="130" rx="10" fill="url(#dark)"/>
{$ports}
SVG;
    }

    private function nvme(string $a): string
    {
        $pins = '';
        for ($i = 0; $i < 9; $i++) {
            $y = 350 + $i * 12;
            $pins .= "<rect x=\"664\" y=\"{$y}\" width=\"26\" height=\"7\" fill=\"#d4a017\"/>";
        }

        return <<<SVG
<rect x="110" y="330" width="570" height="140" rx="10" fill="#0f172a"/>
<circle cx="136" cy="400" r="14" fill="#fff" opacity=".9"/>
<rect x="170" y="345" width="380" height="110" rx="6" fill="url(#accent)"/>
<text x="360" y="412" text-anchor="middle" font-family="Arial Black, Arial, sans-serif" font-size="40" font-weight="900" fill="#fff">NVMe</text>
<rect x="570" y="360" width="70" height="80" rx="4" fill="#1f2937"/>
{$pins}
SVG;
    }

    private function shade(string $hex, float $amount): string
    {
        $hex = ltrim($hex, '#');
        $channels = array_map('hexdec', str_split($hex, 2));

        return '#'.implode('', array_map(
            fn (int $channel): string => str_pad(dechex((int) max(0, min(255, $channel + $channel * $amount))), 2, '0', STR_PAD_LEFT),
            $channels,
        ));
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function plus(int $a, int $b): int
    {
        return $a + $b;
    }

    private function minus(int $a, int $b): int
    {
        return $a - $b;
    }

    private function half(int $a): int
    {
        return intdiv($a, 2);
    }
}
