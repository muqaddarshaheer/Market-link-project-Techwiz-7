@extends('layouts.app')
@section('title', 'FAQs')
@section('content')
<h1 class="section-title">Chatbot FAQs</h1>
<form method="POST" action="{{ route('admin.faqs.store') }}" class="card-ml p-3 mb-3">@csrf
    <input class="form-control mb-2" name="question" placeholder="Question" required>
    <textarea class="form-control mb-2" name="answer" required></textarea>
    <input class="form-control mb-2" name="category" placeholder="Category">
    <input class="form-control mb-2" name="keywords" placeholder="keywords, comma, separated">
    <button class="btn btn-ml">Add FAQ</button>
</form>
@foreach($faqs as $faq)
<div class="card-ml p-3 mb-2"><strong>{{ $faq->question }}</strong><p>{{ $faq->answer }}</p><div class="small muted">{{ implode(', ', $faq->keywords ?? []) }}</div>
<form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger">Delete</button></form></div>
@endforeach
@endsection
