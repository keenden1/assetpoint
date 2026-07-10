@extends('layouts.app')

@section('title', 'Stores')

@section('content')
    <div class="mx-auto" style="max-width: 640px;">
        <div class="mb-4">
            <h1 class="h3 mb-1 page-title">Stores</h1>
            <p class="page-sub mb-0">Branch names suggested in the B.O STORE field.</p>
        </div>

        <div class="card card-elevated mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('stores.store') }}" novalidate>
                    @csrf
                    {{-- Validation errors surface via the SweetAlert popup (partials/alerts);
                         the fields only keep the red outline. --}}
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <input type="text" name="code" value="{{ old('code') }}"
                                class="form-control @error('code') is-invalid @enderror"
                                placeholder="Code (e.g. BW0075)">
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Store name (e.g. BANACOM)">
                                <button type="submit" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Add store</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('stores.index') }}" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                <input type="search" name="search" id="store-search" data-live-search
                    value="{{ request('search') }}" class="form-control" placeholder="Search code or name">
                @if (request('search'))
                    <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary" title="Clear search">
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
                            <th style="width: 110px;">Code</th>
                            <th>Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stores as $store)
                            <tr>
                                <td class="text-secondary font-monospace small">{{ $store->code ?: '—' }}</td>
                                <td class="fw-medium">{{ $store->name }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('stores.destroy', $store) }}" class="d-inline"
                                        data-confirm="Delete {{ $store->name }}{{ $store->code ? ' ('.$store->code.')' : '' }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="icon"><i class="bi bi-{{ request('search') ? 'search' : 'shop' }}"></i></div>
                                        @if (request('search'))
                                            <div>No stores match "{{ request('search') }}".</div>
                                            <a href="{{ route('stores.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                                <i class="bi bi-x-lg me-1"></i>Clear search
                                            </a>
                                        @else
                                            <div>No stores yet. Add your first branch above.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($stores->hasPages())
            <div class="mt-3 small">
                {{ $stores->onEachSide(1)->links() }}
            </div>
        @endif
        </div>{{-- /[data-search-results] --}}
    </div>
@endsection
