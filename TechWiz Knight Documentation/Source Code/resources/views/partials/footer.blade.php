<footer class="footer-ml mt-5 py-5">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <h5 class="d-flex align-items-center gap-2 mb-2"><img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="32" height="32"> {{ $siteName ?? 'MarketLink' }}</h5>
                <p class="mb-2">Reserve local produce in Rs, pick up at the market, and pay the farmer in person.</p>
                @if($siteAddress)<div class="small">{{ $siteAddress }}</div>@endif
                @if($siteEmail)<div class="small"><a href="mailto:{{ $siteEmail }}">{{ $siteEmail }}</a></div>@endif
                @if($sitePhone)<div class="small">{{ $sitePhone }}</div>@endif
            </div>
            <div class="col-6 col-lg-2">
                <h6>Explore</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('markets.index') }}">Markets</a></li>
                    <li><a href="{{ route('farmers.index') }}">Farmers</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Help</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6>Account</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-ml btn-sm" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-outline-ml btn-sm" href="{{ route('register') }}">Create account</a>
                </div>
            </div>
        </div>
        <div class="footer-bar small mt-4 pt-3">© {{ date('Y') }} {{ $siteName ?? 'MarketLink' }} · Prices in Pakistani Rupees (Rs)</div>
    </div>
</footer>
