@php
    $user = auth()->user();
    $avatarUrl = \App\Support\ImageStore::url($user->avatar, 'images/placeholder.svg');
@endphp

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('account.profile') }}" enctype="multipart/form-data" class="card-ml panel-card p-4 h-100">
            @csrf
            @method('PUT')
            <h2 class="h6 mb-3" data-en="Your details" data-ur="آپ کی تفصیل">Your details</h2>

            <div class="account-avatar-row mb-3">
                <img class="account-avatar" src="{{ $avatarUrl }}" alt="" width="72" height="72">
                <div>
                    <label class="form-label mb-1" for="avatar" data-en="Profile photo" data-ur="پروفائل تصویر">Profile photo</label>
                    <input class="form-control" id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                    <p class="small muted mb-0 mt-1" data-en="JPG, PNG or WebP · max 2 MB" data-ur="JPG، PNG یا WebP · زیادہ سے زیادہ ۲ میگابائٹ">JPG, PNG or WebP · max 2 MB</p>
                </div>
            </div>

            <label class="form-label" for="name" data-en="Name" data-ur="نام">Name</label>
            <input class="form-control mb-2" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100">

            <label class="form-label" for="email" data-en="Email" data-ur="ای میل">Email</label>
            <input class="form-control mb-2" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="150">

            <label class="form-label" for="phone" data-en="Phone" data-ur="فون">Phone</label>
            <input class="form-control mb-2" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required maxlength="20">

            <label class="form-label" for="address" data-en="Address" data-ur="پتہ">Address</label>
            <textarea class="form-control mb-3" id="address" name="address" rows="3" maxlength="500">{{ old('address', $user->address) }}</textarea>

            <button class="btn btn-ml" type="submit" data-en="Save profile" data-ur="پروفائل محفوظ کریں">Save profile</button>
        </form>
    </div>

    <div class="col-lg-5">
        <form method="POST" action="{{ route('account.password') }}" class="card-ml panel-card p-4 h-100">
            @csrf
            @method('PUT')
            <h2 class="h6 mb-3" data-en="Change password" data-ur="پاس ورڈ تبدیل کریں">Change password</h2>
            <p class="small muted mb-3" data-en="At least 6 characters. You’ll use this next time you log in." data-ur="کم از کم ۶ حروف۔ اگلی بار لاگ اِن پر یہی استعمال ہوگا۔">At least 6 characters. You’ll use this next time you log in.</p>

            @error('current_password')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <label class="form-label" for="current_password" data-en="Current password" data-ur="موجودہ پاس ورڈ">Current password</label>
            <input class="form-control mb-2" id="current_password" type="password" name="current_password" minlength="6" required autocomplete="current-password">

            <label class="form-label" for="password" data-en="New password" data-ur="نیا پاس ورڈ">New password</label>
            <input class="form-control mb-2" id="password" type="password" name="password" minlength="6" required autocomplete="new-password">

            <label class="form-label" for="password_confirmation" data-en="Confirm password" data-ur="پاس ورڈ تصدیق">Confirm password</label>
            <input class="form-control mb-3" id="password_confirmation" type="password" name="password_confirmation" minlength="6" required autocomplete="new-password">

            <button class="btn btn-outline-ml" type="submit" data-en="Update password" data-ur="پاس ورڈ اپ ڈیٹ کریں">Update password</button>
        </form>
    </div>
</div>
