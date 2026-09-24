<footer class="footer-ml py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h4 class="display-font text-white">MarketLink</h4>
                <p class="mb-0 opacity-75">{{ $siteTagline ?? 'Fresh from local farmers markets' }}</p>
            </div>
            <div class="col-md-2">
                <h6 class="text-uppercase">Explore</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('markets.index') }}">Markets</a></li>
                    <li><a href="{{ route('farmers.index') }}">Farmers</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h6 class="text-uppercase">Help</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase">Pickup only</h6>
                <p class="small opacity-75">No online payments or delivery. Pay in person when you collect your pre-order at the market stall.</p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="d-flex justify-content-between small opacity-75">
            <span>&copy; {{ date('Y') }} MarketLink — Techwiz 7</span>
            <span>Built with Laravel 11</span>
        </div>
    </div>
</footer>
