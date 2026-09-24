@php $market = $market ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $market->name ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">City</label><input class="form-control" name="city" value="{{ old('city', $market->city ?? '') }}" required></div>
    <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ old('address', $market->address ?? '') }}" required></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2">{{ old('description', $market->description ?? '') }}</textarea></div>
    <div class="col-md-3"><label class="form-label">Opening</label><input type="time" class="form-control" name="opening_time" value="{{ old('opening_time', isset($market->opening_time)?substr($market->opening_time,0,5):'08:00') }}"></div>
    <div class="col-md-3"><label class="form-label">Closing</label><input type="time" class="form-control" name="closing_time" value="{{ old('closing_time', isset($market->closing_time)?substr($market->closing_time,0,5):'14:00') }}"></div>
    <div class="col-md-3"><label class="form-label">Latitude</label><input class="form-control" name="latitude" value="{{ old('latitude', $market->latitude ?? '') }}"></div>
    <div class="col-md-3"><label class="form-label">Longitude</label><input class="form-control" name="longitude" value="{{ old('longitude', $market->longitude ?? '') }}"></div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select"><option value="active" @selected(old('status',$market->status??'active')=='active')>active</option><option value="inactive" @selected(old('status',$market->status??'')=='inactive')>inactive</option></select>
    </div>
    <div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="col-12">
        <label class="form-label d-block">Operating days</label>
        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
            <label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, old('operating_days', $market->operating_days ?? [])))> {{ substr($day,0,3) }}</label>
        @endforeach
    </div>
</div>
