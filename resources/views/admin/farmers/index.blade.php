@extends('layouts.app')
@section('title', 'Farmers')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Farmer approvals</h1>
    <form method="GET" class="mb-3">
        <select name="approval_status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(['pending','approved','rejected'] as $s)<option value="{{ $s }}" @selected(request('approval_status')==$s)>{{ $s }}</option>@endforeach
        </select>
    </form>
    @foreach($farmers as $farmer)
        <div class="panel mb-3">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">{{ $farmer->stall_name }}</h5>
                    <p class="mb-1 small text-muted">{{ $farmer->user->name }} · {{ $farmer->user->email }} · {{ $farmer->approval_status }}</p>
                    <p class="mb-0">{{ Str::limit($farmer->business_description, 160) }}</p>
                </div>
                <div class="d-flex gap-2 align-items-start">
                    @if($farmer->approval_status !== 'approved')
                        <form method="POST" action="{{ route('admin.farmers.approve', $farmer) }}">@csrf<button class="btn btn-success btn-sm">Approve</button></form>
                    @endif
                    @if($farmer->approval_status !== 'rejected')
                        <form method="POST" action="{{ route('admin.farmers.reject', $farmer) }}">@csrf
                            <input type="hidden" name="rejection_reason" value="Does not meet marketplace guidelines.">
                            <button class="btn btn-outline-danger btn-sm">Reject</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    {{ $farmers->links() }}
</div>
@endsection
