@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<h1 class="section-title mb-4">Settings</h1>

<section class="mb-4">
    <h2 class="h5 mb-3">Your admin account</h2>
    @include('partials.account-forms')
</section>

<section class="mb-4">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="card-ml panel-card p-4">
        @csrf
        @method('PUT')
        <h2 class="h6 mb-3">Platform settings</h2>
        @foreach([
            'platform_name' => 'Platform name',
            'tagline' => 'Tagline',
            'contact_email' => 'Contact email',
            'contact_phone' => 'Phone',
            'contact_address' => 'Address',
            'facebook' => 'Facebook URL',
            'instagram' => 'Instagram URL',
        ] as $key => $label)
            <label class="form-label" for="{{ $key }}">{{ $label }}</label>
            <input
                class="form-control mb-2"
                id="{{ $key }}"
                name="{{ $key }}"
                value="{{ old($key, $settings[$key] ?? '') }}"
                {{ in_array($key, ['facebook', 'instagram', 'tagline'], true) ? '' : 'required' }}
            >
        @endforeach
        <button class="btn btn-ml" type="submit">Save settings</button>
    </form>
</section>
@endsection
