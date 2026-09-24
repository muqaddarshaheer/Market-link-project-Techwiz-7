@extends('layouts.app')
@section('title', 'Farmers')
@section('content')
<h1 class="section-title">Farmers</h1>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Stall name"></div>
    <div class="col-md-4"><select class="form-select" name="day"><option value="">Any day</option>@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option @selected(request('day')===$day)>{{ $day }}</option>@endforeach</select></div>
    <div class="col-md-3"><button class="btn btn-ml">Filter</button></div>
</form>
<div class="row g-3">
    @forelse($farmers as $farmer)
        <div class="col-md-6 col-lg-4">@include('partials.farmer-card', compact('farmer'))</div>
    @empty
        <div class="empty-state"><i class="bi bi-shop"></i><p>No stalls match that search.</p></div>
    @endforelse
</div>
<div class="mt-3">{{ $farmers->links() }}</div>
@endsection
