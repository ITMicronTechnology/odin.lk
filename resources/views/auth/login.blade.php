@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="auth-wrap">
        <div class="container" style="max-width: 980px">
            <div class="auth-card row g-0">
                <div class="col-md-5 d-none d-md-block">@include('auth.partials.side', ['headline' => 'Welcome back!'])</div>
                <div class="col-md-7">
                    <div class="auth-form">
                        <h3 class="fw-bold mb-1">Login to your account</h3>
                        <p class="text-muted mb-4">New to Odin.lk? <a href="{{ route('register') }}" class="fw-semibold">Create an account</a></p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="email">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                                <label class="form-check-label" for="remember">Keep me logged in</label>
                            </div>
                            <button class="btn btn-odin btn-lg w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
