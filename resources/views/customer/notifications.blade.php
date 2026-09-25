@extends(auth()->user()->isFarmer() ? 'layouts.farmer' : 'layouts.customer')
@section('title', 'Notifications')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Inbox</p>
        <h1 class="section-title mb-0">Notifications</h1>
    </div>
    <form method="POST" action="{{ auth()->user()->isFarmer() ? route('farmer.notifications.read-all') : route('customer.notifications.read-all') }}">@csrf
        <button class="btn btn-outline-ml btn-sm" type="submit">Mark all read</button>
    </form>
</div>
@forelse($notifications as $note)
    <div class="panel-list-item card-ml panel-card p-3 mb-2 {{ $note->is_read ? '' : 'is-unread' }}">
        <div class="d-flex justify-content-between gap-2"><strong>{{ $note->title }}</strong><span class="small muted">{{ $note->created_at?->diffForHumans() }}</span></div>
        <p class="mb-1 mt-1">{{ $note->message }}</p>
        @if(!$note->is_read && auth()->user()->isCustomer())
            <form method="POST" action="{{ route('customer.notifications.read', $note) }}">@csrf<button class="btn btn-link btn-sm px-0" type="submit">Mark read</button></form>
        @endif
    </div>
@empty
    <div class="empty-state card-ml panel-card"><i class="bi bi-bell"></i><p>No notifications yet.</p></div>
@endforelse
{{ $notifications->links() }}
@endsection
