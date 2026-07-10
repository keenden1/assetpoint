@extends('layouts.guest')

@section('title', 'Register')
@section('heading', 'Create an account')

{{-- Inline field errors only — suppress the global SweetAlert error popup. --}}
@section('suppressErrorAlert', true)

@section('content')
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                class="form-control @error('password') is-invalid @enderror">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">At least 8 characters, with upper &amp; lower case and a number.</div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password" class="form-control">
        </div>

        <button type="submit" class="btn btn-dark w-100">Create account</button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">
        Already registered? <a href="{{ route('login') }}">Sign in</a>
    </p>
@endsection
