@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="display-font h3">Notifications</h1>
        <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn btn-sm btn-outline-primary">Mark all read</button></form>
    </div>
    @forelse($notifications as $n)
        <div class="panel mb-2 {{ $n->is_read ? '' : 'border-success' }}">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>{{ $n->title }}</strong>
                    <p class="mb-1">{{ $n->message }}</p>
                    <small class="text-muted">{{ $n->created_at?->diffForHumans() }}</small>
                </div>
                @unless($n->is_read)
                    <form method="POST" action="{{ route('notifications.read', $n) }}">@csrf<button class="btn btn-sm btn-link">Mark read</button></form>
                @endunless
            </div>
        </div>
    @empty
        <div class="empty-state">No notifications.</div>
    @endforelse
    {{ $notifications->links() }}
</div>
@endsection
