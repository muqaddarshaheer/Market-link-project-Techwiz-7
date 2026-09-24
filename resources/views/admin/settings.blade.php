@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<h1 class="section-title">Settings</h1>
<form method="POST" action="{{ route('admin.settings.update') }}" class="card-ml p-4">@csrf @method('PUT')
    @foreach(['platform_name'=>'Platform name','tagline'=>'Tagline','contact_email'=>'Contact email','contact_phone'=>'Phone','contact_address'=>'Address','facebook'=>'Facebook URL','instagram'=>'Instagram URL'] as $key=>$label)
        <label class="form-label">{{ $label }}</label>
        <input class="form-control mb-2" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" {{ in_array($key, ['facebook','instagram','tagline']) ? '' : 'required' }}>
    @endforeach
    <button class="btn btn-ml">Save</button>
</form>
@endsection
