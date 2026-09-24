<footer class="footer-ml mt-5 py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5>{{ $siteName ?? 'MarketLink' }}</h5>
                <p class="mb-0">Pre-order from local farmers and pick up at the market. Payment happens in person.</p>
            </div>
            <div class="col-md-4">
                <h6>Explore</h6>
                <ul class="list-unstyled">
                    <li><a class="link-light" href="{{ route('markets.index') }}">Markets</a></li>
                    <li><a class="link-light" href="{{ route('products.index') }}">Products</a></li>
                    <li><a class="link-light" href="{{ route('sitemap') }}">Sitemap</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Account</h6>
                <ul class="list-unstyled">
                    <li><a class="link-light" href="{{ route('login') }}">Log in</a></li>
                    <li><a class="link-light" href="{{ route('register') }}">Register</a></li>
                </ul>
            </div>
        </div>
        <div class="small mt-4">© {{ date('Y') }} MarketLink. Fresh food, short miles.</div>
    </div>
</footer>
