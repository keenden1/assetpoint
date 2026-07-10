@extends('layouts.app')

@section('title', 'Remarks')

@section('content')
    <div class="mx-auto" style="max-width: 640px;">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
            <div>
                <h1 class="h3 mb-1 page-title">Remarks</h1>
                <p class="page-sub mb-0">Preset options for the B.O entry Remarks dropdown.</p>
            </div>
            <a href="{{ route('bo.index') }}" class="text-decoration-none small text-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to B.O
            </a>
        </div>

        <div class="card card-elevated mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('remarks.store') }}" novalidate>
                    @csrf
                    {{-- Validation errors surface via the SweetAlert popup (partials/alerts);
                         the field only keeps the red outline. --}}
                    <div class="input-group">
                        <span class="input-group-text bg-body"><i class="bi bi-chat-left-text"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Remark (e.g. TOAST)">
                        <button type="submit" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Add remark</button>
                    </div>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('remarks.index') }}" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                <input type="search" name="search" id="remark-search" data-live-search
                    value="{{ request('search') }}" class="form-control" placeholder="Search remarks">
                @if (request('search'))
                    <a href="{{ route('remarks.index') }}" class="btn btn-outline-secondary" title="Clear search">
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
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($remarks as $remark)
                            <tr>
                                <td class="fw-medium">{{ $remark->name }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('remarks.destroy', $remark) }}" class="d-inline"
                                        data-confirm="Delete the remark {{ $remark->name }}? Existing BO entries keep their saved remark text.">
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
                                <td colspan="2">
                                    <div class="empty-state">
                                        <div class="icon"><i class="bi bi-{{ request('search') ? 'search' : 'chat-left-text' }}"></i></div>
                                        @if (request('search'))
                                            <div>No remarks match "{{ request('search') }}".</div>
                                            <a href="{{ route('remarks.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                                <i class="bi bi-x-lg me-1"></i>Clear search
                                            </a>
                                        @else
                                            <div>No remarks yet. Add your first one above (e.g. TOAST).</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($remarks->hasPages())
            <div class="mt-3 small">
                {{ $remarks->onEachSide(1)->links() }}
            </div>
        @endif
        </div>{{-- /[data-search-results] --}}
    </div>
@endsection
