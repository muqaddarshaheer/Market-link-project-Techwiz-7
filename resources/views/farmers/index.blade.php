@extends('layouts.app')
@section('title', 'Trusted Farmers')
@section('content')
@php use App\Support\ImageStore; @endphp
<section class="farmer-dir">
    <header class="farmer-dir-head">
        <p class="farmer-dir-trust"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Verified growers</p>
        <h1 class="section-title mb-1">Trusted Farmers</h1>
        <p class="farmer-dir-sub">Discover verified farmers and connect directly with the people who grow your food.</p>
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
                $photo = $farmer->logo
                    ? ImageStore::picture($farmer->logo, $farmer->stall_name)
                    : asset('images/farmers/male-farmer.jpg');
                $crop = $farmer->products->take(3)->pluck('name')->filter()->implode(', ')
                    ?: ($farmer->products->first()?->category?->name ?: 'Seasonal produce');
                $line = \Illuminate\Support\Str::limit($farmer->business_description ?: 'Approved stall. Reserve produce and pay at pickup.', 90);
                $payload = [
                    'name' => $farmer->contact_person ?: ($farmer->user->name ?? $farmer->stall_name),
                    'farm' => $farmer->stall_name,
                    'location' => $farmer->address ?: ($farmer->markets->pluck('name')->filter()->implode(', ') ?: 'Local market'),
                    'crop' => $crop,
                    'bio' => $farmer->business_description ?: 'This grower sells at the market. Reserve ahead, then pick up and pay in person.',
                    'photo' => $photo,
                    'phone' => $farmer->user->phone ?? '',
                    'days' => implode(', ', $farmer->operating_days ?? []) ?: 'See stall for market days',
                    'products' => $farmer->products->pluck('name')->take(6)->implode(', ') ?: 'Seasonal produce',
                    'count' => (int) ($farmer->products_count ?? $farmer->products->count()),
                    'profile' => route('farmers.show', $farmer),
                    'contact' => route('contact'),
                ];
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <button type="button" class="farmer-dir-card" data-farmer='@json($payload)'>
                    <span class="farmer-dir-cover">
                        <img src="{{ $photo }}" alt="{{ $farmer->stall_name }}" width="640" height="360" loading="lazy" decoding="async">
                        <span class="farmer-dir-badge">Verified</span>
                    </span>
                    <span class="farmer-dir-body">
                        <span class="farmer-dir-name">{{ $payload['name'] }}</span>
                        <span class="farmer-dir-farm">{{ $farmer->stall_name }}</span>
                        <span class="farmer-dir-meta"><i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $payload['location'] }}</span>
                        <span class="farmer-dir-crop">{{ $crop }}</span>
                        <span class="farmer-dir-meta">{{ $farmer->user->phone ?? '' }}</span>
                        <span class="farmer-dir-line">{{ $line }}</span>
                        <span class="farmer-dir-cta">View Profile</span>
                    </span>
                </button>
            </div>
        @empty
            <div class="empty-state"><i class="bi bi-shop"></i><p>No stalls match that search.</p></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $farmers->links() }}</div>
</section>
<div id="farmerProfileModal" hidden></div>
@endsection
@push('scripts')
<script>
(function () {
    const root = document.getElementById('farmerProfileModal');
    const cards = Array.from(document.querySelectorAll('.farmer-dir-card'));
    if (!root || !cards.length) return;
    let lastTrigger = null;
    let currentIndex = 0;

    function closeModal() {
        root.hidden = true;
        root.innerHTML = '';
        document.body.style.overflow = '';
        if (lastTrigger) lastTrigger.focus();
    }

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
        });
    }

    function openModal(data, trigger) {
        lastTrigger = trigger || null;
        root.hidden = false;
        document.body.style.overflow = 'hidden';
        data = {
            name: esc(data.name), farm: esc(data.farm), location: esc(data.location),
            crop: esc(data.crop), bio: esc(data.bio), photo: esc(data.photo),
            days: esc(data.days), products: esc(data.products), count: esc(data.count),
            phone: esc(data.phone || ''), profile: esc(data.profile), contact: esc(data.contact)
        };
        root.innerHTML =
            '<div class="farmer-modal" role="dialog" aria-modal="true" aria-labelledby="farmer-modal-title">' +
            '<div class="farmer-modal-backdrop" data-close></div>' +
            '<div class="farmer-modal-card">' +
            '<div class="farmer-modal-cover"><img src="' + data.photo + '" alt="' + data.farm + '"></div>' +
            '<button type="button" class="farmer-modal-x" data-close aria-label="Close farmer profile">×</button>' +
            '<div class="farmer-modal-body">' +
            '<h2 id="farmer-modal-title">' + data.name + ' <span class="farmer-dir-badge">Verified</span></h2>' +
            '<p class="farmer-dir-farm">' + data.farm + '</p>' +
            '<p class="farmer-dir-meta"><i class="bi bi-geo-alt" aria-hidden="true"></i> ' + data.location + '</p>' +
            '<p class="farmer-dir-crop">' + data.crop + '</p>' +
            '<p>' + data.bio + '</p>' +
            '<dl class="farmer-modal-facts">' +
            '<div><dt>Market days</dt><dd>' + data.days + '</dd></div>' +
            '<div><dt>Phone</dt><dd>' + (data.phone || 'Ask at the stall') + '</dd></div>' +
            '<div><dt>Products</dt><dd>' + data.products + '</dd></div>' +
            '<div><dt>Listed items</dt><dd>' + data.count + '</dd></div>' +
            '</dl>' +
            '<div class="farmer-modal-nav">' +
            '<button type="button" class="btn btn-outline-ml" data-prev' + (cards.length < 2 ? ' disabled' : '') + '>Previous</button>' +
            '<span>' + (currentIndex + 1) + ' of ' + cards.length + '</span>' +
            '<button type="button" class="btn btn-outline-ml" data-next' + (cards.length < 2 ? ' disabled' : '') + '>Next</button>' +
            '</div>' +
            '<div class="d-flex gap-2 flex-wrap">' +
            '<a class="btn btn-ml" href="' + data.contact + '">Contact Farmer</a>' +
            '<a class="btn btn-outline-ml" href="' + data.profile + '">View Products</a>' +
            '</div></div></div></div>';
        const dialog = root.querySelector('.farmer-modal');
        dialog.addEventListener('click', function (event) {
            if (event.target.closest('[data-close]')) closeModal();
            if (event.target.closest('[data-prev]')) showAt(currentIndex - 1);
            if (event.target.closest('[data-next]')) showAt(currentIndex + 1);
        });
        root.querySelector('.farmer-modal-x').focus();
    }

    function showAt(index) {
        const total = cards.length;
        currentIndex = (index + total) % total;
        const card = cards[currentIndex];
        lastTrigger = card;
        openModal(JSON.parse(card.getAttribute('data-farmer')), card);
    }

    cards.forEach(function (card, index) {
        card.addEventListener('click', function () {
            currentIndex = index;
            openModal(JSON.parse(card.getAttribute('data-farmer')), card);
        });
    });

    document.addEventListener('keydown', function (event) {
        const dialog = root.querySelector('.farmer-modal');
        if (!dialog) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }
        if (event.key === 'ArrowRight') { event.preventDefault(); showAt(currentIndex + 1); return; }
        if (event.key === 'ArrowLeft') { event.preventDefault(); showAt(currentIndex - 1); return; }
        if (event.key !== 'Tab') return;
        const items = dialog.querySelectorAll('button, a[href]');
        if (!items.length) return;
        const first = items[0];
        const last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
})();
</script>
@endpush
