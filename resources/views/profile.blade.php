@extends('layouts.app')

@section('title', 'My profile')

@section('content')
    <div class="mx-auto" style="max-width: 640px;">
        <div class="mb-4">
            <h1 class="h3 mb-1 page-title">My profile</h1>
            <p class="page-sub mb-0">Update your account details and password.</p>
        </div>

        {{-- ===================== Profile information ===================== --}}
        <div class="card card-elevated mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-person-circle me-1 text-secondary"></i>Profile information</h2>

                <form method="POST" action="{{ route('user-profile-information.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" name="name" type="text" required autocomplete="name"
                            value="{{ old('name', auth()->user()->name) }}"
                            class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror">
                        @error('name', 'updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" required autocomplete="username"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror">
                        @error('email', 'updateProfileInformation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Changing your email will require verifying the new address.</div>
                    </div>

                    <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check-lg me-1"></i>Save changes</button>
                </form>
            </div>
        </div>

        {{-- ===================== Change password ===================== --}}
        <div class="card card-elevated">
            <div class="card-body p-4">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-shield-lock me-1 text-secondary"></i>Change password</h2>

                <form method="POST" action="{{ route('user-password.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                        @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                        @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">At least 8 characters, with upper &amp; lower case and a number.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            class="form-control">
                    </div>

                    <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check-lg me-1"></i>Update password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
