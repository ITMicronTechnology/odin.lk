@extends('account.layout', ['pageTitle' => 'Profile & Password'])

@section('title', 'My Profile')

@section('account')
    <div class="section-box mb-4">
        <h5 class="fw-bold mb-3">Personal Details & Address</h5>
        <form method="POST" action="{{ route('account.profile.update') }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label" for="name">Full name</label>
                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Mobile number</label>
                <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="address">Street address</label>
                <input id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="city">City</label>
                <input id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $user->city) }}">
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="district">District</label>
                <select id="district" name="district" class="form-select @error('district') is-invalid @enderror">
                    <option value="">Select district</option>
                    @foreach (config('shop.districts') as $district)
                        <option value="{{ $district }}" @selected(old('district', $user->district) === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="postal_code">Postal code</label>
                <input id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $user->postal_code) }}">
                @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><button class="btn btn-odin">Save Changes</button></div>
        </form>
    </div>

    <div class="section-box">
        <h5 class="fw-bold mb-3">Change Password</h5>
        <form method="POST" action="{{ route('account.password.update') }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-4">
                <label class="form-label" for="current_password">Current password</label>
                <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="new_password">New password</label>
                <input id="new_password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
            <div class="col-12"><button class="btn btn-odin-dark">Update Password</button></div>
        </form>
    </div>
@endsection
