@extends('layouts.app')
@section('title', 'Admin markets')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-3"><h1 class="h3 display-font">Markets</h1><a href="{{ route('admin.markets.create') }}" class="btn btn-primary">Add market</a></div>
    <div class="panel table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>City</th><th>Status</th><th>Farmers</th><th>Products</th><th></th></tr></thead>
            <tbody>
            @foreach($markets as $market)
                <tr>
                    <td>{{ $market->name }}</td><td>{{ $market->city }}</td><td>{{ $market->status }}</td>
                    <td>{{ $market->farmers_count }}</td><td>{{ $market->products_count }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.markets.edit', $market) }}">Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('admin.markets.destroy', $market) }}">@csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0" onclick="return confirm('Delete market?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $markets->links() }}
</div>
@endsection
