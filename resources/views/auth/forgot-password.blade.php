@extends('layouts.guest')

@section('title', 'Forgot password')
@section('heading', 'Reset your password')

{{-- Inline field errors only — suppress the global SweetAlert error popup. --}}
@section('suppressErrorAlert', true)

@section('content')
    <p class="text-secondary small">Enter your email and we'll send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-dark w-100" data-loading-text="Sending reset link…">Email password reset link</button>
    </form>

    <p class="text-center small mt-4 mb-0"><a href="{{ route('login') }}">Back to sign in</a></p>
@endsection
