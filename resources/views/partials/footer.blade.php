<footer class="footer-ml mt-5 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="d-flex align-items-center gap-2"><img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="32" height="32"> {{ $siteName ?? 'MarketLink' }}</h5>
                <p class="mb-1">Pre-order from local farmers and pick up at the market. Payment happens in person.</p>
                @if($siteAddress)<div>{{ $siteAddress }}</div>@endif
                @if($siteEmail)<div><a href="mailto:{{ $siteEmail }}">{{ $siteEmail }}</a></div>@endif
                @if($sitePhone)<div>{{ $sitePhone }}</div>@endif
                <div class="mt-2">
                    @if($siteFacebook)<a class="me-2" href="{{ $siteFacebook }}">Facebook</a>@endif
                    @if($siteInstagram)<a href="{{ $siteInstagram }}">Instagram</a>@endif
                </div>
            </div>
            <div class="col-md-4">
                <h6>Explore</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('markets.index') }}">Markets</a></li>
                    <li><a href="{{ route('farmers.index') }}">Farmers</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Account</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('login') }}">Log in</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
                </ul>
            </div>
        </div>
        <div class="small mt-4">© {{ date('Y') }} {{ $siteName ?? 'MarketLink' }}. Fresh food, short miles.</div>
    </div>
</footer>
