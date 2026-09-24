@extends('layouts.app')
@section('title', 'Chatbot FAQs')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Chatbot FAQs</h1>
    <form method="POST" action="{{ route('admin.faqs.store') }}" class="panel mb-4">
        @csrf
        <div class="mb-2"><input class="form-control" name="question" placeholder="Question" required></div>
        <div class="mb-2"><textarea class="form-control" name="answer" rows="3" placeholder="Answer" required></textarea></div>
        <div class="row g-2">
            <div class="col-md-4"><input class="form-control" name="category" placeholder="Category"></div>
            <div class="col-md-6"><input class="form-control" name="keywords" placeholder="keywords, comma, separated"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
        </div>
    </form>
    @foreach($faqs as $faq)
        <div class="panel mb-2 d-flex justify-content-between">
            <div><strong>{{ $faq->question }}</strong><p class="mb-0 small">{{ $faq->answer }}</p></div>
            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
        </div>
    @endforeach
    {{ $faqs->links() }}
</div>
@endsection
