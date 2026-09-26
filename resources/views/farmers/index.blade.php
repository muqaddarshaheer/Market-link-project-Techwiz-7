@extends('layouts.app')
@section('title', 'Trusted Farmers')
@section('content')
@php use App\Support\ImageStore; @endphp
<section class="farmer-dir">
    <header class="farmer-dir-head">
        <p class="farmer-dir-trust"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Verified growers</p>
        <h1 class="section-title mb-1">Trusted Farmers</h1>
        <p class="farmer-dir-sub">Tap a card for products, market days, and direct stall contact.</p>
    </header>
    <form class="farmer-dir-search" method="GET" role="search">
        <label class="visually-hidden" for="farmer-q">Search farmers or crops</label>
        <input id="farmer-q" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search farmers or crops">
        @if(request('day'))<input type="hidden" name="day" value="{{ request('day') }}">@endif
        <button class="btn btn-ml" type="submit">Search</button>
    </form>
    <div class="row g-3">
        @forelse($farmers as $farmer)
            @php
                $photo = ImageStore::picture($farmer->logo, $farmer->stall_name);
                $crop = $farmer->products->take(3)->pluck('name')->filter()->implode(', ')
                    ?: ($farmer->products->first()?->category?->name ?: 'Seasonal produce');
                $line = \Illuminate\Support\Str::limit($farmer->business_description ?: 'Approved stall. Reserve produce and pay at pickup.', 90);
                $phone = $farmer->user->phone ?? '';
                $markets = $farmer->markets->map(fn ($m) => trim($m->name . ($m->pivot->stall_number ? ' · Stall '.$m->pivot->stall_number : '')))->filter()->values()->all();
                $days = collect($farmer->operating_days ?? [])->filter()->values()->all();
                $productNames = $farmer->products->pluck('name')->filter()->take(8)->values()->all();
                $rating = $farmer->rating_avg ? round((float) $farmer->rating_avg, 1) : null;
                $payload = [
                    'name' => $farmer->contact_person ?: ($farmer->user->name ?? $farmer->stall_name),
                    'farm' => $farmer->stall_name,
                    'location' => $farmer->address ?: (implode(', ', $markets) ?: 'Local market'),
                    'crop' => $crop,
                    'bio' => $farmer->business_description ?: 'This grower sells at the market. Reserve ahead, then pick up and pay in person.',
                    'photo' => $photo,
                    'phone' => $phone,
                    'days' => $days,
                    'markets' => $markets,
                    'products' => $productNames,
                    'count' => (int) ($farmer->products_count ?? $farmer->products->count()),
                    'rating' => $rating,
                    'profile' => route('farmers.show', $farmer),
                    'shop' => route('products.index', ['farmer' => $farmer->id]),
                    'contact' => route('contact'),
                ];
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <article class="farmer-dir-card is-live" data-farmer="{{ json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" role="button" tabindex="0" aria-haspopup="dialog">
                    <span class="farmer-dir-cover">
                        <img src="{{ $photo }}" alt="{{ $farmer->stall_name }}" width="640" height="360" loading="lazy" decoding="async">
                        <span class="farmer-dir-badge">Verified</span>
                    </span>
                    <span class="farmer-dir-body">
                        <span class="farmer-dir-name">{{ $payload['name'] }}</span>
                        <span class="farmer-dir-farm">{{ $farmer->stall_name }}</span>
                        <span class="farmer-dir-meta"><i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $payload['location'] }}</span>
                        <span class="farmer-dir-crop">{{ $crop }}</span>
                        @if($phone)
                            <a class="farmer-dir-phone" href="tel:{{ preg_replace('/\s+/', '', $phone) }}" onclick="event.stopPropagation()">
                                <i class="bi bi-telephone-fill" aria-hidden="true"></i> {{ $phone }}
                            </a>
                        @endif
                        <span class="farmer-dir-line">{{ $line }}</span>
                        <span class="farmer-dir-cta">Open profile</span>
                    </span>
                </article>
            </div>
        @empty
            <div class="empty-state"><i class="bi bi-shop"></i><p>No stalls match that search.</p></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $farmers->links() }}</div>
</section>
@endsection
@push('scripts')
<script>
(function () {
    const cards = Array.from(document.querySelectorAll('.farmer-dir-card[data-farmer]'));
    if (!cards.length) return;

    let root = document.getElementById('farmerProfileModal');
    if (!root) {
        root = document.createElement('div');
        root.id = 'farmerProfileModal';
        root.hidden = true;
        document.body.appendChild(root);
    } else if (root.parentElement !== document.body) {
        document.body.appendChild(root);
    }

    let lastTrigger = null;
    let currentIndex = 0;

    function closeModal() {
        root.hidden = true;
        root.innerHTML = '';
        document.body.classList.remove('farmer-modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
        });
    }

    function readData(card) {
        try {
            return JSON.parse(card.getAttribute('data-farmer') || '{}');
        } catch (err) {
            return null;
        }
    }

    function listHtml(items, emptyText) {
        const list = Array.isArray(items) ? items.filter(Boolean) : String(items || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
        if (!list.length) return '<span class="farmer-modal-empty">' + esc(emptyText) + '</span>';
        return '<ul class="farmer-modal-chips">' + list.map(function (item) {
            return '<li>' + esc(item) + '</li>';
        }).join('') + '</ul>';
    }

    function starsHtml(rating) {
        if (!rating) return '';
        const full = Math.round(Number(rating));
        let html = '<span class="farmer-modal-stars" aria-label="' + esc(rating) + ' out of 5">';
        for (let i = 1; i <= 5; i++) {
            html += '<i class="bi bi-star' + (i <= full ? '-fill' : '') + '" aria-hidden="true"></i>';
        }
        html += '<span>' + esc(rating) + '</span></span>';
        return html;
    }

    function openModal(data, trigger) {
        if (!data) return;
        lastTrigger = trigger || null;
        root.hidden = false;
        document.body.classList.add('farmer-modal-open');

        const phone = esc(data.phone || '');
        const tel = phone.replace(/\s+/g, '');
        const name = esc(data.name);
        const farm = esc(data.farm);
        const location = esc(data.location);
        const bio = esc(data.bio);
        const photo = esc(data.photo);
        const count = esc(data.count);
        const profile = esc(data.profile);
        const shop = esc(data.shop || data.profile);
        const contact = esc(data.contact);
        const days = listHtml(data.days, 'See stall for market days');
        const markets = listHtml(data.markets, 'Ask at the stall');
        const products = listHtml(data.products, 'Seasonal produce');
        const rating = starsHtml(data.rating);

        root.innerHTML =
            '<div class="farmer-modal" role="dialog" aria-modal="true" aria-labelledby="farmer-modal-title">' +
            '<div class="farmer-modal-backdrop" data-close tabindex="-1"></div>' +
            '<div class="farmer-modal-card">' +
            '<button type="button" class="farmer-modal-x" data-close aria-label="Close farmer profile">&times;</button>' +
            '<div class="farmer-modal-cover"><img src="' + photo + '" alt="' + farm + '"></div>' +
            '<div class="farmer-modal-body">' +
            '<div class="farmer-modal-topline"><span class="farmer-modal-verified"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Verified</span>' + rating + '</div>' +
            '<h2 id="farmer-modal-title">' + name + '</h2>' +
            '<p class="farmer-modal-stall">' + farm + '</p>' +
            '<p class="farmer-modal-bio">' + bio + '</p>' +
            '<div class="farmer-modal-contact">' +
            '<div><i class="bi bi-geo-alt-fill" aria-hidden="true"></i><span>' + location + '</span></div>' +
            (phone
                ? '<div><i class="bi bi-telephone-fill" aria-hidden="true"></i><a href="tel:' + tel + '">' + phone + '</a></div>'
                : '<div><i class="bi bi-telephone" aria-hidden="true"></i><span>Ask at the stall</span></div>') +
            '<div><i class="bi bi-basket2-fill" aria-hidden="true"></i><span>' + count + ' listed items</span></div>' +
            '</div>' +
            '<div class="farmer-modal-grid">' +
            '<section><h3>Market days</h3>' + days + '</section>' +
            '<section><h3>Markets</h3>' + markets + '</section>' +
            '<section class="farmer-modal-products"><h3>Products</h3>' + products + '</section>' +
            '</div>' +
            '<div class="farmer-modal-nav">' +
            '<button type="button" class="btn btn-outline-ml" data-prev' + (cards.length < 2 ? ' disabled' : '') + '>Previous</button>' +
            '<span>' + (currentIndex + 1) + ' of ' + cards.length + '</span>' +
            '<button type="button" class="btn btn-outline-ml" data-next' + (cards.length < 2 ? ' disabled' : '') + '>Next</button>' +
            '</div>' +
            '<div class="farmer-modal-actions">' +
            (phone ? '<a class="btn btn-ml" href="tel:' + tel + '"><i class="bi bi-telephone-fill" aria-hidden="true"></i> Call farmer</a>' : '') +
            '<a class="btn btn-outline-ml" href="' + shop + '">Browse products</a>' +
            '<a class="btn btn-outline-ml" href="' + profile + '">Full profile</a>' +
            '<a class="btn btn-outline-ml" href="' + contact + '">Message MarketLink</a>' +
            '</div></div></div></div>';

        root.querySelector('.farmer-modal').addEventListener('click', function (event) {
            if (event.target.closest('[data-close]')) {
                closeModal();
                return;
            }
            if (event.target.closest('[data-prev]')) showAt(currentIndex - 1);
            if (event.target.closest('[data-next]')) showAt(currentIndex + 1);
        });
        root.querySelector('.farmer-modal-x').focus();
    }

    function showAt(index) {
        const total = cards.length;
        currentIndex = (index + total) % total;
        const card = cards[currentIndex];
        openModal(readData(card), card);
    }

    cards.forEach(function (card, index) {
        card.addEventListener('click', function (event) {
            if (event.target.closest('a')) return;
            currentIndex = index;
            openModal(readData(card), card);
        });
        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                currentIndex = index;
                openModal(readData(card), card);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (root.hidden) return;
        if (event.key === 'Escape') closeModal();
        if (event.key === 'ArrowLeft') showAt(currentIndex - 1);
        if (event.key === 'ArrowRight') showAt(currentIndex + 1);
    });
})();
</script>
@endpush
