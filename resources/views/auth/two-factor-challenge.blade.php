@extends('layouts.guest')

@section('title', 'Two-factor confirmation')
@section('heading', 'Two-factor confirmation')

{{-- Inline field errors only — suppress the global SweetAlert error popup. --}}
@section('suppressErrorAlert', true)

@section('content')
    <p class="text-secondary small">
        Confirm access to your account by entering the authentication code provided by your authenticator application,
        or one of your emergency recovery codes.
    </p>

    <form method="POST" action="{{ route('two-factor.login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="code" class="form-label">Authentication code</label>
            <input id="code" name="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code"
                class="form-control @error('code') is-invalid @enderror">
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="recovery_code" class="form-label">Recovery code</label>
            <input id="recovery_code" name="recovery_code" type="text" autocomplete="one-time-code"
                class="form-control @error('recovery_code') is-invalid @enderror">
            @error('recovery_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-dark w-100">Confirm</button>
    </form>
@endsection
