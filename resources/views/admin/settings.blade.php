@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<h1 class="section-title">Settings</h1>
<form method="POST" action="{{ route('admin.pin.update') }}" class="card-ml p-4 mb-3">@csrf
    <h2 class="h6">Change admin PIN</h2>
    <div class="d-flex gap-2 flex-wrap">
        <input class="form-control" style="max-width:140px" name="current_pin" inputmode="numeric" maxlength="4" required placeholder="Current">
        <input class="form-control" style="max-width:140px" name="pin" inputmode="numeric" maxlength="4" required placeholder="New PIN">
        <input class="form-control" style="max-width:160px" name="pin_confirmation" inputmode="numeric" maxlength="4" required placeholder="Confirm">
        <button class="btn btn-ml">Update PIN</button>
    </div>
</form>
<form method="POST" action="{{ route('admin.settings.update') }}" class="card-ml p-4">@csrf @method('PUT')
    @foreach(['platform_name'=>'Platform name','tagline'=>'Tagline','contact_email'=>'Contact email','contact_phone'=>'Phone','contact_address'=>'Address','facebook'=>'Facebook URL','instagram'=>'Instagram URL'] as $key=>$label)
        <label class="form-label">{{ $label }}</label>
        <input class="form-control mb-2" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" {{ in_array($key, ['facebook','instagram','tagline']) ? '' : 'required' }}>
    @endforeach
    <button class="btn btn-ml">Save</button>
</form>
@endsection
