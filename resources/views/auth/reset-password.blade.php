@extends('layouts.guest')

@section('title', 'Reset password')
@section('heading', 'Choose a new password')

{{-- Inline field errors only — suppress the global SweetAlert error popup. --}}
@section('suppressErrorAlert', true)

@section('content')
    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus
                autocomplete="username" class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
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

        <button type="submit" class="btn btn-dark w-100">Reset password</button>
    </form>
@endsection
