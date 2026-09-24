@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Users</h1>
    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search"></div>
        <div class="col-md-3"><select name="role" class="form-select"><option value="">Role</option>@foreach(['admin','farmer','customer'] as $r)<option value="{{ $r }}" @selected(request('role')==$r)>{{ $r }}</option>@endforeach</select></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">Status</option>@foreach(['active','inactive','pending','suspended'] as $s)<option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    </form>
    <div class="panel table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td>{{ $user->status }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.status', $user) }}" class="d-flex gap-1">
                            @csrf
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['active','inactive','pending','suspended'] as $s)<option value="{{ $s }}" @selected($user->status==$s)>{{ $s }}</option>@endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
