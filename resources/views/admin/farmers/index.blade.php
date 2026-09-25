@extends('layouts.admin')
@section('title', 'Farmers')
@section('content')
<div class="desk-head panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">People</p>
        <h1 class="section-title mb-1">Farmers</h1>
        <p class="muted mb-0">Approve stalls, update contact details, and keep listings honest.</p>
    </div>
</div>
<form class="desk-toolbar" method="GET">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search stall">
    <select class="form-select" name="status">
        <option value="">All statuses</option>
        @foreach(['pending','approved','rejected'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="btn btn-ml">Filter</button>
</form>
<div class="row g-3">
@forelse($farmers as $farmer)
    <div class="col-md-6">
        <article class="card-ml p-3 h-100 desk-card">
            <div class="d-flex justify-content-between gap-2 mb-2">
                <div>
                    <a class="fw-bold text-decoration-none" href="{{ route('admin.farmers.show', $farmer) }}">{{ $farmer->stall_name }}</a>
                    <div class="small muted">{{ $farmer->user->name }} · {{ $farmer->user->phone }}</div>
                    <div class="small muted">{{ $farmer->user->email }}</div>
                </div>
                <span class="badge badge-soft">{{ $farmer->approval_status }}</span>
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="approved"><button class="btn btn-sm btn-ml">Approve</button></form>
                <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="rejected"><button class="btn btn-sm btn-outline-danger">Reject</button></form>
                <form method="POST" action="{{ route('admin.farmers.suspend', $farmer) }}">@csrf<button class="btn btn-sm btn-outline-ml">Suspend</button></form>
                <a class="btn btn-sm btn-outline-ml" href="{{ route('admin.farmers.show', $farmer) }}">Edit</a>
                <form method="POST" action="{{ route('admin.farmers.destroy', $farmer) }}" onsubmit="return confirm('Delete {{ $farmer->stall_name }}? This removes the stall, products, and login.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
            </div>
        </article>
    </div>
@empty
    <p class="muted">No farmers match these filters.</p>
@endforelse
</div>
{{ $farmers->links() }}
@endsection
