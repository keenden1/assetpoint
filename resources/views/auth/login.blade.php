@extends('layouts.guest')

@section('title', 'Sign in')
@section('heading', 'Sign in to your account')

{{-- Login uses inline field errors only — suppress the global SweetAlert error popup. --}}
@section('suppressErrorAlert', true)

@section('content')
    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-dark w-100">Sign in</button>
    </form>

    @if (Route::has('register'))
        <p class="text-center text-secondary small mt-4 mb-0">
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </p>
    @endif
@endsection
