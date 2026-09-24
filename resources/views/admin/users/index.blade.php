@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<h1 class="section-title">Customers</h1>
<form class="mb-3" method="GET"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name or email"></form>
<div class="table-responsive card-ml"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Status</th><th></th></tr></thead>
<tbody>@foreach($users as $user)<tr><td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></td><td>{{ $user->email }}</td><td>{{ $user->status }}</td><td><form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf<button class="btn btn-sm btn-outline-ml">{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}</button></form></td></tr>@endforeach</tbody></table></div>
{{ $users->links() }}
@endsection
