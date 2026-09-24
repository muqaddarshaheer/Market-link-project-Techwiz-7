@extends('layouts.app')
@section('title', 'Farmers')
@section('content')
<h1 class="section-title">Farmers</h1>
@foreach($farmers as $farmer)
<div class="card-ml p-3 mb-2 d-flex justify-content-between flex-wrap gap-2">
    <div><strong>{{ $farmer->stall_name }}</strong><div class="small muted">{{ $farmer->user->email }} · {{ $farmer->approval_status }} · account {{ $farmer->user->status }}</div></div>
    <div class="d-flex gap-1">
        <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="approved"><button class="btn btn-sm btn-ml">Approve</button></form>
        <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="rejected"><button class="btn btn-sm btn-outline-danger">Reject</button></form>
        <form method="POST" action="{{ route('admin.farmers.suspend', $farmer) }}">@csrf<button class="btn btn-sm btn-outline-ml">Suspend/restore</button></form>
    </div>
</div>
@endforeach
{{ $farmers->links() }}
@endsection
