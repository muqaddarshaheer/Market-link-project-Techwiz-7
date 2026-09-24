@extends(auth()->user()->isFarmer() ? 'layouts.farmer' : 'layouts.customer')
@section('title', 'Notifications')
@section('content')
<div class="d-flex justify-content-between"><h1 class="section-title">Notifications</h1>
<form method="POST" action="{{ auth()->user()->isFarmer() ? route('farmer.notifications.read-all') : route('customer.notifications.read-all') }}">@csrf<button class="btn btn-outline-ml btn-sm">Mark all read</button></form></div>
@forelse($notifications as $note)
    <div class="card-ml p-3 mb-2">
        <div class="d-flex justify-content-between"><strong>{{ $note->title }}</strong><span class="small muted">{{ $note->created_at?->diffForHumans() }}</span></div>
        <p class="mb-1">{{ $note->message }}</p>
        @if(!$note->is_read && auth()->user()->isCustomer())
            <form method="POST" action="{{ route('customer.notifications.read', $note) }}">@csrf<button class="btn btn-link btn-sm">Mark read</button></form>
        @endif
    </div>
@empty
    <div class="empty-state card-ml"><i class="bi bi-bell"></i><p>No notifications yet.</p></div>
@endforelse
{{ $notifications->links() }}
@endsection
