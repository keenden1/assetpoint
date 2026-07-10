@extends('layouts.app')

@section('title', 'Account status')

@section('content')
    <div class="mx-auto text-center" style="max-width: 480px;">
        <div class="card card-elevated">
            <div class="card-body p-4 p-sm-5">
                @if (auth()->user()->status === 'pending')
                    <div class="empty-state py-0">
                        <div class="icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                    <h1 class="h4 mt-2">Awaiting approval</h1>
                    <p class="text-secondary">
                        Thanks for registering, {{ auth()->user()->name }}! An administrator needs to
                        approve your account before you can use {{ config('app.name') }}.
                        Please check back later.
                    </p>
                @else
                    <div class="empty-state py-0">
                        <div class="icon bg-danger-subtle text-danger-emphasis"><i class="bi bi-slash-circle"></i></div>
                    </div>
                    <h1 class="h4 mt-2">Account disabled</h1>
                    <p class="text-secondary">
                        Your account has been disabled. If you believe this is a mistake,
                        please contact the administrator.
                    </p>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-right me-1"></i>Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
