@php
    $map = [
        'placed' => 'text-bg-primary',
        'accepted' => 'text-bg-info',
        'declined' => 'text-bg-danger',
        'ready_for_pickup' => 'text-bg-warning',
        'completed' => 'text-bg-success',
        'cancelled' => 'text-bg-secondary',
    ];
@endphp
<span class="badge {{ $map[$status] ?? 'text-bg-secondary' }}">{{ str_replace('_', ' ', $status) }}</span>
