@php
    $farWing = 'M69 31 C71 18 70 5 65 -10 L63.8 -3 L61 -5 L61.8 2 L58.8 1.5 L60 8.5 L57 9 L59 15.5 L56 17.5 Q59 24 60 32 Z';
    $body = 'M92 34 L87 32.4 Q85 27.2 80 27 Q75 27 72 31 Q62 32 50 34 Q44 35 38 36.5 L22 33 Q18.5 34.5 20 37 Q17 39 20 41 Q18 44 22 45 L38 42 Q46 47 58 46.5 Q70 45.5 78 39 Q83 35.5 87 35.2 Z';
    $nearWing = 'M64 33 C63 18 56 3 42 -14 L44 -6 L40 -8 L43 -1 L39 -2 L43 5 L39.5 5.5 L44.5 12 L41.5 13.5 L47 19 L45 22 Q48 29 50 36 Z';

    /*
     * Glowing smoke ribbons that stream out behind each raven (drawn pointing right, away from the beak).
     * [path, gradient, opacity, billow speed in seconds]
     */
    $smoke = [
        ['M0 30 C30 22 55 18 85 26 C115 34 140 44 175 36 C210 28 240 14 300 22 L300 44 C245 40 215 52 178 50 C140 48 112 40 84 36 C56 32 30 36 0 32 Z', 'odinSmokeFill', .75, .9],
        ['M4 29 C35 16 70 6 110 14 C150 22 170 8 210 4 C240 1 270 10 300 6 L300 12 C268 18 242 10 212 14 C176 18 152 30 110 22 C72 16 40 24 4 31 Z', 'odinSmokeFill', .6, .7],
        ['M4 32 C38 40 70 54 118 50 C160 46 190 58 230 56 C258 55 282 48 300 52 L300 58 C280 56 258 62 228 62 C188 62 158 52 118 56 C70 60 36 46 4 33 Z', 'odinSmokeFill', .6, .8],
        ['M2 31 C32 27 60 34 96 31 C130 28 150 20 186 24 C214 27 232 36 262 33 L262 36 C232 40 212 31 186 29 C152 26 132 34 96 35 C60 38 32 31 2 32 Z', 'odinSmokeFill', .45, .6],
        ['M0 30.5 C40 26 80 30 120 30 C170 30 210 22 260 26 L262 28 C212 25 170 33 120 32.5 C80 32 40 29 0 31.5 Z', 'odinSmokeCore', 1, .5],
    ];

    /*
     * Six ravens in a loose diagonal flock.
     * k = size (1 = about 66px on a laptop), lx/ly = the raven's lane offset in px,
     * arc = how much its path bows, delay/duration in seconds, flap = one wing beat.
     */
    $ravens = [
        ['k' => 1.1, 'lx' => 0, 'ly' => 0, 'arc' => 30, 'delay' => 0, 'duration' => 3.2, 'flap' => .52, 'phase' => 0],
        ['k' => .85, 'lx' => 80, 'ly' => -70, 'arc' => 45, 'delay' => .22, 'duration' => 3.35, 'flap' => .48, 'phase' => -.18],
        ['k' => .95, 'lx' => -70, 'ly' => 80, 'arc' => 20, 'delay' => .4, 'duration' => 3.25, 'flap' => .5, 'phase' => -.3],
        ['k' => .7, 'lx' => 160, 'ly' => -10, 'arc' => 55, 'delay' => .6, 'duration' => 3.5, 'flap' => .44, 'phase' => -.1],
        ['k' => .8, 'lx' => -20, 'ly' => 150, 'arc' => 35, 'delay' => .78, 'duration' => 3.4, 'flap' => .46, 'phase' => -.24],
        ['k' => .6, 'lx' => 100, 'ly' => 100, 'arc' => 50, 'delay' => .95, 'duration' => 3.6, 'flap' => .42, 'phase' => -.06],
    ];
@endphp
<div class="odin-preloader" role="status" aria-label="Loading Odin.lk"></div>

<div class="odin-flock" id="odinFlock" aria-hidden="true">
    {{-- Shared gradients and the "wispy smoke" filter (turbulence that slowly shifts so the smoke curls). --}}
    <svg class="odin-smoke-defs" width="0" height="0" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="odinSmokeFill" x1="0" x2="1" y1="0" y2="0">
                <stop offset="0" stop-color="#f1ffb0" stop-opacity=".95"/>
                <stop offset=".14" stop-color="#b6ff3d" stop-opacity=".9"/>
                <stop offset=".42" stop-color="#39ff14" stop-opacity=".6"/>
                <stop offset=".72" stop-color="#16c400" stop-opacity=".28"/>
                <stop offset="1" stop-color="#0a8f00" stop-opacity="0"/>
            </linearGradient>
            <linearGradient id="odinSmokeCore" x1="0" x2="1" y1="0" y2="0">
                <stop offset="0" stop-color="#fbffe0"/>
                <stop offset=".5" stop-color="#d8ff6a" stop-opacity=".8"/>
                <stop offset="1" stop-color="#9dff3a" stop-opacity="0"/>
            </linearGradient>
            @foreach ([['odinWispA', 4, '1.5s'], ['odinWispB', 9, '1.9s']] as [$filterId, $seed, $speed])
                <filter id="{{ $filterId }}" x="-10%" y="-90%" width="130%" height="280%">
                    <feTurbulence type="fractalNoise" baseFrequency="0.02 0.09" numOctaves="2" seed="{{ $seed }}" result="noise">
                        <animate attributeName="baseFrequency" dur="{{ $speed }}" values="0.02 0.09;0.028 0.125;0.017 0.08;0.02 0.09" repeatCount="indefinite"/>
                    </feTurbulence>
                    <feDisplacementMap in="SourceGraphic" in2="noise" scale="20" xChannelSelector="R" yChannelSelector="G" result="wisp"/>
                    <feGaussianBlur in="wisp" stdDeviation="2.6" result="glow"/>
                    <feMerge><feMergeNode in="glow"/><feMergeNode in="wisp"/></feMerge>
                </filter>
            @endforeach
        </defs>
    </svg>

    @foreach ($ravens as $raven)
        <div class="odin-raven" style="--k: {{ $raven['k'] }}; --lx: {{ $raven['lx'] }}px; --ly: {{ $raven['ly'] }}px; --arc: {{ $raven['arc'] }}px; --delay: {{ $raven['delay'] }}s; --duration: {{ $raven['duration'] }}s; --flap: {{ $raven['flap'] }}s; --phase: {{ $raven['phase'] }}s">
            <div class="tilt">
                <svg class="smoke" viewBox="0 0 300 60" xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#{{ $loop->even ? 'odinWispA' : 'odinWispB' }})">
                        @foreach ($smoke as [$d, $fill, $opacity, $speed])
                            <path class="rib" d="{{ $d }}" fill="url(#{{ $fill }})" opacity="{{ $opacity }}" style="--billow: {{ $speed }}s; --billow-delay: -{{ ($loop->index * .17) }}s"/>
                        @endforeach
                    </g>
                </svg>
                <svg class="bird-svg" viewBox="10 -20 90 72" xmlns="http://www.w3.org/2000/svg">
                    <g class="bird" fill="#0b0b0f">
                        <path class="wing far" d="{{ $farWing }}"/>
                        <path d="{{ $body }}"/>
                        <path class="wing near" d="{{ $nearWing }}"/>
                    </g>
                </svg>
            </div>
        </div>
    @endforeach
</div>
<script>
    (function () {
        var root = document.documentElement;
        if (!root.classList.contains('show-preloader')) { return; }

        var preloader = document.querySelector('.odin-preloader');
        var lastRavenFlying = false;
        var pageReady = document.readyState !== 'loading';
        var revealed = false;

        // The raven that starts last / flies longest is the last one to leave.
        var timings = Array.prototype.map.call(document.querySelectorAll('#odinFlock .odin-raven'), function (el) {
            var delay = parseFloat(el.style.getPropertyValue('--delay'));
            var duration = parseFloat(el.style.getPropertyValue('--duration'));
            return { el: el, delay: delay, duration: duration, end: delay + duration };
        }).sort(function (a, b) { return b.end - a.end; });
        var last = timings[0] || { delay: 0, duration: 1, end: 1 };

        // The page fades in while the last raven flies its final stretch, fully visible as it leaves the screen.
        var revealAt = (last.delay + last.duration * .65) * 1000;
        var fadeTime = last.duration * .35;

        function reveal() {
            if (revealed || !lastRavenFlying || !pageReady) { return; }
            revealed = true;
            preloader.style.transitionDuration = fadeTime + 's';
            root.classList.add('preloader-revealed');
            setTimeout(finish, fadeTime * 1000 + 200);
        }

        function finish() {
            if (revealed) { root.classList.remove('show-preloader', 'preloader-revealed'); }
        }

        setTimeout(function () { lastRavenFlying = true; reveal(); }, revealAt);

        if (!pageReady) {
            document.addEventListener('DOMContentLoaded', function () { pageReady = true; reveal(); });
        }

        if (last.el) {
            last.el.addEventListener('animationend', function (event) {
                if (event.animationName === 'odin-fly') { setTimeout(finish, 150); }
            });
        }

        // Safety net in case an animation event never fires (e.g. the tab was in the background).
        setTimeout(function () { lastRavenFlying = true; pageReady = true; reveal(); }, last.end * 1000 + 600);
        setTimeout(finish, last.end * 1000 + 1600);
    })();
</script>
