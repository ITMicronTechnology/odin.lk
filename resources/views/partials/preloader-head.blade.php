{{-- Raven preloader: shown when someone arrives on the site or reloads a page (not on in-site link clicks). --}}
<script>
    (function () {
        try {
            var entry = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
            var isReload = entry ? entry.type === 'reload' : (performance.navigation && performance.navigation.type === 1);
            var cameFromElsewhere = !document.referrer || new URL(document.referrer).origin !== location.origin;
            var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if ((isReload || cameFromElsewhere) && !reducedMotion) {
                document.documentElement.classList.add('show-preloader');
            }
        } catch (error) {}
    })();
</script>
<style>
    .odin-preloader, .odin-flock { display: none; }

    /* White screen behind the flock; fades out once the flock has crossed and the page is ready. */
    html.show-preloader .odin-preloader {
        display: block;
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: #fff;
        transition: opacity .8s cubic-bezier(.4, 0, .2, 1);
        animation: odin-failsafe .01s linear 7s forwards;
    }
    html.show-preloader:not(.preloader-revealed) body { overflow: hidden; }
    html.preloader-revealed .odin-preloader { opacity: 0; pointer-events: none; }

    html.show-preloader .odin-flock {
        display: block;
        position: fixed;
        inset: 0;
        z-index: 2001;
        pointer-events: none;
        overflow: hidden;
        animation: odin-failsafe .01s linear 7s forwards;
    }
    .odin-smoke-defs { position: absolute; width: 0; height: 0; }

    /* One raven: the outer box flies across the screen, the inner parts tilt, bob and flap. */
    .odin-raven {
        position: absolute;
        left: 0;
        top: 0;
        width: calc(var(--k) * clamp(34px, 4.6vw, 72px));
        opacity: 0;
        will-change: transform;
        animation: odin-fly var(--duration) cubic-bezier(.3, .05, .6, .95) var(--delay) forwards;
    }
    .odin-raven .tilt { position: relative; transform: rotate(24deg); }   /* nose up towards the top-left */
    .odin-raven .bird-svg {
        position: relative;
        display: block;
        width: 100%;
        height: auto;
        overflow: visible;
        transform: scaleX(-1);
        filter: drop-shadow(0 0 1.5px #39ff14) drop-shadow(0 0 5px rgba(57, 255, 20, .7));
    }
    .odin-raven .bird { animation: odin-bob var(--flap) ease-in-out var(--phase) infinite; }
    .odin-raven .wing { transform-box: view-box; animation: var(--flap) cubic-bezier(.45, 0, .55, 1) var(--phase) infinite; }
    .odin-raven .wing.near { transform-origin: 57px 34px; animation-name: odin-flap-near; }
    .odin-raven .wing.far { transform-origin: 64.5px 31.5px; animation-name: odin-flap-far; }

    /* Glowing green smoke streaming from the raven's back; it unfurls as the bird enters and leaves with it. */
    .odin-raven .smoke {
        position: absolute;
        left: 48%;
        top: -18%;
        width: 560%;
        height: auto;
        overflow: visible;
        transform-origin: 0 50%;
        filter: drop-shadow(0 0 5px rgba(57, 255, 20, .55));
        animation: odin-smoke-in 1s cubic-bezier(.2, .7, .3, 1) var(--delay) both;
    }
    .odin-raven .smoke .rib {
        transform-box: fill-box;
        transform-origin: 0 50%;
        animation: odin-billow var(--billow) ease-in-out var(--billow-delay) infinite alternate;
    }

    /* Bottom-right corner → middle (slight arc) → top-left corner. Each raven keeps its own lane. */
    @keyframes odin-fly {
        0% { opacity: 0; transform: translate(calc(100vw + 90px + var(--lx)), calc(100vh + 60px + var(--ly))) scale(.9); }
        7% { opacity: 1; }
        50% { transform: translate(calc(50vw + var(--lx) + var(--arc)), calc(47vh + var(--ly) - var(--arc))) scale(1); }
        93% { opacity: 1; }
        100% { opacity: 0; transform: translate(calc(-10vw - 90px + var(--lx)), calc(-12vh - 60px + var(--ly))) scale(.92); }
    }
    /* Wing beat: wings high in a V, powerful downstroke, then back up. */
    @keyframes odin-flap-near {
        0%, 100% { transform: rotate(0deg) scaleY(1); }
        20% { transform: rotate(-6deg) scaleY(.55); }
        42% { transform: rotate(14deg) scaleY(-.75); }
        65% { transform: rotate(6deg) scaleY(-.2); }
        85% { transform: rotate(2deg) scaleY(.62); }
    }
    @keyframes odin-flap-far {
        0%, 100% { transform: scaleY(1); }
        20% { transform: scaleY(.6); }
        42% { transform: scaleY(-.6); }
        65% { transform: scaleY(-.1); }
        85% { transform: scaleY(.66); }
    }
    @keyframes odin-bob {
        0%, 100% { transform: translateY(1.5px); }
        42% { transform: translateY(-2px); }
    }
    @keyframes odin-smoke-in {
        from { transform: scaleX(.15) scaleY(.6); opacity: 0; }
        to { transform: none; opacity: 1; }
    }
    /* Each ribbon swells and ripples at its own pace, so the smoke looks alive. */
    @keyframes odin-billow {
        from { transform: scale(.96, .85) skewY(1.5deg); }
        to { transform: scale(1.05, 1.3) skewY(-2.5deg); }
    }
    @keyframes odin-failsafe { to { opacity: 0; visibility: hidden; } }
</style>
