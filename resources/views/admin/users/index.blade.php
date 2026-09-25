@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<div class="desk-head">
    <div>
        <h1 class="section-title mb-1">Customers</h1>
        <p class="muted mb-0">Accounts that reserve produce for market pickup.</p>
    </div>
</div>
<form class="desk-toolbar" method="GET">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name or email">
    <button class="btn btn-ml">Search</button>
</form>
<div class="table-responsive card-ml">
<table class="table mb-0">
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($users as $user)
        <tr>
            <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td><span class="badge badge-soft">{{ $user->status }}</span></td>
            <td>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf
                    <button class="btn btn-sm btn-outline-ml">{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="p-4 muted">No customers found.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $users->links() }}
@endsection
