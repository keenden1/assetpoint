@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="mb-4">
        <h1 class="h3 mb-1 page-title">Users</h1>
        <p class="page-sub mb-0">Approve new registrations and enable or disable accounts.</p>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
            <input type="search" name="search" id="user-search" data-live-search
                value="{{ request('search') }}" class="form-control" placeholder="Search name or email">
            @if (request('search'))
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Clear search">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>

    <div data-search-results>
    <div class="card card-elevated overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-app">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Verified</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-medium">
                                {{ $user->name }}
                                @if ($user->id === auth()->id())
                                    <span class="text-secondary small">(you)</span>
                                @endif
                            </td>
                            <td class="text-secondary">{{ $user->email }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $user->is_admin
                                    ? 'bg-primary-subtle text-primary-emphasis'
                                    : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    <i class="bi bi-{{ $user->is_admin ? 'shield-check' : 'person' }} me-1"></i>{{ $user->is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td>
                                @if ($user->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success" title="Email verified"></i>
                                @else
                                    <i class="bi bi-dash-circle text-secondary" title="Not verified"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ [
                                    'pending' => 'bg-warning-subtle text-warning-emphasis',
                                    'active' => 'bg-success-subtle text-success-emphasis',
                                    'disabled' => 'bg-secondary-subtle text-secondary-emphasis',
                                ][$user->status] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="text-end text-nowrap">
                                @if ($user->id !== auth()->id())
                                    @unless ($user->is_admin)
                                        @if ($user->status === 'pending')
                                            <form method="POST" action="{{ route('users.approve', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-lg me-1"></i>Approve
                                                </button>
                                            </form>
                                        @elseif ($user->status === 'active')
                                            <form method="POST" action="{{ route('users.toggle', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-slash-circle me-1"></i>Disable
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('users.toggle', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Enable
                                                </button>
                                            </form>
                                        @endif
                                    @endunless

                                    @if ($user->is_admin || $user->status === 'active')
                                        <form method="POST" action="{{ route('users.role', $user) }}" class="d-inline"
                                            data-confirm="{{ $user->is_admin
                                                ? $user->name.' will lose admin access and become a regular user.'
                                                : $user->name.' will become an admin and be able to manage users.' }}"
                                            data-confirm-button="{{ $user->is_admin ? 'Remove admin' : 'Make admin' }}"
                                            data-confirm-color="#212529">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $user->is_admin ? 'btn-outline-secondary' : 'btn-outline-primary' }}">
                                                <i class="bi bi-{{ $user->is_admin ? 'person-down' : 'shield-plus' }} me-1"></i>{{ $user->is_admin ? 'Remove admin' : 'Make admin' }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="icon"><i class="bi bi-{{ request('search') ? 'search' : 'people' }}"></i></div>
                                    @if (request('search'))
                                        <div>No users match "{{ request('search') }}".</div>
                                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                            <i class="bi bi-x-lg me-1"></i>Clear search
                                        </a>
                                    @else
                                        <div>No users yet.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($users->hasPages())
        <div class="mt-3 small">
            {{ $users->onEachSide(1)->links() }}
        </div>
    @endif
    </div>{{-- /[data-search-results] --}}
@endsection
