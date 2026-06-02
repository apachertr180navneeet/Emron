@extends('admin.layouts.login_layout')
@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark">Forgot Password?</h3>
    <p class="text-muted" style="font-size:.875rem">Enter your email and we'll send you a reset link</p>
</div>
@if(session('status'))
    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
@endif
<form action="{{route('admin.forget.password.post')}}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary">
                <i class="bi bi-envelope"></i>
            </span>
            <input class="form-control ps-5 @error('email') is-invalid @enderror" id="email" type="email" name="email" placeholder="Your email" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;box-shadow:0 4px 16px rgba(79,70,229,.3)">Send Reset Link</button>
</form>
<p class="text-center mt-3 mb-0" style="font-size:.875rem">
    <a href="{{route('admin.login')}}" class="text-decoration-none fw-semibold"><i class="bi bi-arrow-left me-1"></i>Back to login</a>
</p>
@endsection