@extends('admin.layouts.login_layout')
@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark">Reset Password</h3>
    <p class="text-muted" style="font-size:.875rem">Enter your new password below</p>
</div>
<form method="POST" action="{{ route('admin.reset.password.post') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" class="form-control ps-5 @error('password') is-invalid @enderror" name="password" required placeholder="New password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="password-confirm" class="form-label">Confirm Password</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="bi bi-lock-fill"></i></span>
            <input id="password-confirm" type="password" class="form-control ps-5" name="password_confirmation" required placeholder="Confirm password">
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;box-shadow:0 4px 16px rgba(79,70,229,.3)">Reset Password</button>
</form>
<p class="text-center mt-3 mb-0" style="font-size:.875rem">
    <a href="{{route('admin.login')}}" class="text-decoration-none fw-semibold"><i class="bi bi-arrow-left me-1"></i>Back to login</a>
</p>
@endsection