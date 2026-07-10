@extends('layouts.guest')

@section('title', 'Verify email')
@section('heading', 'Verify your email address')

@section('content')
    <p class="text-secondary small">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just
        emailed to you. If you didn't receive it, we'll gladly send another.
    </p>

    <div class="d-flex align-items-center justify-content-between gap-3 mt-3">
        <form method="POST" action="{{ route('verification.send') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-dark">Resend verification email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-link text-secondary p-0">Log out</button>
        </form>
    </div>
@endsection
