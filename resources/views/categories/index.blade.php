@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="mx-auto" style="max-width: 640px;">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
            <div>
                <h1 class="h3 mb-1 page-title">Categories</h1>
                <p class="page-sub mb-0">Groups used to organize the pricelist.</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-decoration-none small text-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to products
            </a>
        </div>

        <div class="card card-elevated mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('categories.store') }}" novalidate>
                    @csrf
                    {{-- Validation errors surface via the SweetAlert popup (partials/alerts);
                         the field only keeps the red outline. --}}
                    <div class="input-group">
                        <span class="input-group-text bg-body"><i class="bi bi-tags"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Category name (e.g. LOAVES)">
                        <button type="submit" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Add category</button>
                    </div>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('categories.index') }}" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                <input type="search" name="search" id="category-search" data-live-search
                    value="{{ request('search') }}" class="form-control" placeholder="Search categories">
                @if (request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary" title="Clear search">
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
                            <th>Products</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="fw-medium">{{ $category->name }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline"
                                        data-confirm="Delete {{ $category->name }}? Its {{ $category->products_count }} product(s) will be deleted too.">
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
                                        <div class="icon"><i class="bi bi-{{ request('search') ? 'search' : 'tags' }}"></i></div>
                                        @if (request('search'))
                                            <div>No categories match "{{ request('search') }}".</div>
                                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                                <i class="bi bi-x-lg me-1"></i>Clear search
                                            </a>
                                        @else
                                            <div>No categories yet. Add your first one above.</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($categories->hasPages())
            <div class="mt-3 small">
                {{ $categories->onEachSide(1)->links() }}
            </div>
        @endif
        </div>{{-- /[data-search-results] --}}
    </div>
@endsection
