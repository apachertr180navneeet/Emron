@extends('admin.layouts.login_layout')
@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark">Create Account</h3>
    <p class="text-muted" style="font-size:.875rem">Register a new admin account for the ERP suite.</p>
</div>
<form method="POST" action="{{ route('admin.register') }}">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required type="text" placeholder="First name">
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required type="text" placeholder="Last name">
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required type="text" placeholder="Username">
        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required type="email" placeholder="Email address">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <input class="form-control @error('password') is-invalid @enderror" name="password" required type="password" placeholder="Password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Confirm Password</label>
            <input class="form-control" name="password_confirmation" required type="password" placeholder="Confirm password">
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;box-shadow:0 4px 16px rgba(79,70,229,.3)">Register</button>
</form>
<p class="text-center mt-3 mb-0" style="font-size:.875rem">
    <a href="{{route('admin.login')}}" class="text-decoration-none fw-semibold">Already have an account? Sign in</a>
</p>
@endsection