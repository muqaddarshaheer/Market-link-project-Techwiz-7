@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Announcements</h1>
    <form method="POST" action="{{ route('admin.announcements.store') }}" class="panel mb-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6"><input class="form-control" name="title" placeholder="Title" required></div>
            <div class="col-md-3">
                <select name="status" class="form-select"><option value="draft">draft</option><option value="published">published</option><option value="archived">archived</option></select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select"><option value="low">low</option><option value="medium" selected>medium</option><option value="high">high</option></select>
            </div>
            <div class="col-12"><textarea class="form-control" name="message" rows="3" required placeholder="Message"></textarea></div>
            <div class="col-md-4"><input type="datetime-local" class="form-control" name="expires_at"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Publish</button></div>
        </div>
    </form>
    @foreach($announcements as $a)
        <div class="panel mb-2">
            <strong>{{ $a->title }}</strong> <span class="badge bg-secondary">{{ $a->status }}</span>
            <p class="mb-1">{{ $a->message }}</p>
            <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
        </div>
    @endforeach
    {{ $announcements->links() }}
</div>
@endsection
