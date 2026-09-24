@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
<h1 class="section-title">Announcements</h1>
<form method="POST" action="{{ route('admin.announcements.store') }}" class="card-ml p-3 mb-4">@csrf
    <input class="form-control mb-2" name="title" placeholder="Title" required>
    <textarea class="form-control mb-2" name="message" required></textarea>
    <div class="row g-2">
        <div class="col"><select class="form-select" name="status"><option>draft</option><option>published</option><option>archived</option></select></div>
        <div class="col"><select class="form-select" name="priority"><option>low</option><option selected>medium</option><option>high</option></select></div>
        <div class="col"><input class="form-control" type="datetime-local" name="published_at"></div>
        <div class="col"><input class="form-control" type="datetime-local" name="expires_at"></div>
    </div>
    <button class="btn btn-ml mt-2">Publish</button>
</form>
@foreach($announcements as $item)
<form method="POST" action="{{ route('admin.announcements.update', $item) }}" class="card-ml p-3 mb-2">@csrf @method('PUT')
    <input class="form-control mb-2" name="title" value="{{ $item->title }}">
    <textarea class="form-control mb-2" name="message">{{ $item->message }}</textarea>
    <div class="row g-2">
        <div class="col"><select class="form-select" name="status">@foreach(['draft','published','archived'] as $s)<option @selected($item->status===$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col"><select class="form-select" name="priority">@foreach(['low','medium','high'] as $s)<option @selected($item->priority===$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col"><input class="form-control" type="datetime-local" name="published_at" value="{{ optional($item->published_at)->format('Y-m-d\TH:i') }}"></div>
        <div class="col"><input class="form-control" type="datetime-local" name="expires_at" value="{{ optional($item->expires_at)->format('Y-m-d\TH:i') }}"></div>
    </div>
    <button class="btn btn-outline-ml btn-sm mt-2">Save</button>
</form>
<form method="POST" action="{{ route('admin.announcements.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger">Delete</button></form>
@endforeach
{{ $announcements->links() }}
@endsection
