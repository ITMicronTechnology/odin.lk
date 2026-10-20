@php($pageLinks = \App\Http\Controllers\PageController::PAGES)
<footer class="site-footer">
    {{-- Service highlights --}}
    <div class="service-bar">
        <div class="container">
            <div class="service-bar-grid">
                <div><i class="bi bi-headset"></i><span>Customer Support<small>{{ config('shop.support_hours') }}</small></span></div>
                <div><i class="bi bi-truck"></i><span>Island-wide Delivery<small>To all 25 districts</small></span></div>
                <div><i class="bi bi-lightning-charge"></i><span>Express Delivery<small>Colombo in 1 - 2 days</small></span></div>
                <div><i class="bi bi-patch-check"></i><span>Genuine Warranty<small>100% original products</small></span></div>
            </div>
        </div>
    </div>

    <div class="footer-main">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-xl-3">
                    @include('partials.logo')
                    <address class="footer-address">{!! nl2br(e(str_replace(', ', ",\n", config('shop.address')))) !!}</address>
                    <div class="footer-hotline"><small>HOTLINE</small><a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}">{{ config('shop.hotline') }}</a></div>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 col-xl-3">
                    <div class="footer-buttons">
                        <a href="{{ route('orders.track') }}"><i class="bi bi-box-seam"></i> Track your Order</a>
                        <a href="{{ route('pages.show', 'contact-us') }}"><i class="bi bi-geo-alt"></i> Visit Our Store</a>
                        <a href="{{ route('services.request') }}"><i class="bi bi-tools"></i> Request IT Service</a>
                        <a href="{{ route('services.portfolio') }}"><i class="bi bi-file-earmark-pdf"></i> Download Portfolio</a>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <h6>About</h6>
                    <ul>
                        <li><a href="{{ route('pages.show', 'about-us') }}">About Us</a></li>
                        <li><a href="{{ route('pages.show', 'contact-us') }}">Contact Us</a></li>
                        <li><a href="{{ route('services.index') }}">IT Services</a></li>
                        <li><a href="{{ route('shop.brands') }}">Brands</a></li>
                        <li><a href="{{ route('shop.deals') }}">Hot Deals</a></li>
                    </ul>
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <h6>Help</h6>
                    <ul>
                        @foreach ($pageLinks as $slug => [$label, $icon, $group])
                            @if ($group === 'help')<li><a href="{{ route('pages.show', $slug) }}">{{ $label }}</a></li>@endif
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-4 col-lg-1 col-xl-2">
                    <h6>Policies</h6>
                    <ul>
                        @foreach ($pageLinks as $slug => [$label, $icon, $group])
                            @if ($group === 'policies')<li><a href="{{ route('pages.show', $slug) }}">{{ $label }}</a></li>@endif
                        @endforeach
                        <li><a href="{{ route('account.index') }}">My Account</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="payment-partners">
        <div class="container">
            <div class="payment-partners-title">
                <span><i class="bi bi-shield-lock-fill me-1"></i> We Accept &amp; Payment Partners</span>
            </div>
            <div class="partners-marquee">
                {{-- The list is repeated once so the scrolling line loops seamlessly. --}}
                <div class="partners-track">
                    @foreach ([false, true] as $isCopy)
                        <ul class="partners-list" @if ($isCopy) aria-hidden="true" @endif>
                            @foreach (config('shop.payment_partners') as $logo => $partner)
                                <li class="partner-tile" title="{{ $partner }}">
                                    <img src="{{ asset('images/payment-partners/'.$logo) }}" alt="{{ $isCopy ? '' : $partner }}" loading="lazy" height="40">
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>Copyright &copy; {{ date('Y') }} Odin.lk. All Rights Reserved.</div>
            <div><i class="bi bi-lock-fill me-1"></i> Secure payments &middot; Cash on Delivery available islandwide</div>
        </div>
    </div>
</footer>
