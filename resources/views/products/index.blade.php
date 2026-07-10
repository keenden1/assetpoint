@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 page-title">Products</h1>
            <p class="page-sub mb-0">Your pricelist — used by the B.O sheet.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-tags me-1"></i>Categories
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-dark">
                <i class="bi bi-plus-lg me-1"></i>Add product
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="card card-elevated mb-4">
        <div class="card-body py-3">
            <div class="row g-2">
                <div class="col-sm-4">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Any status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" id="product-search" data-live-search
                            value="{{ request('search') }}" class="form-control"
                            placeholder="Search product, SKU, or category">
                        @if (request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Clear filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @else
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div data-search-results>
    <div class="card card-elevated overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-app">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th class="text-end">Price</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="text-secondary font-monospace small">{{ $product->sku }}</td>
                            <td class="fw-medium">{{ $product->name }}</td>
                            <td><span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis fw-medium">{{ $product->category->name }}</span></td>
                            <td class="text-end fw-semibold">₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $product->status === 'active' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ $product->remarks ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary border-0" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                                    data-confirm="Delete {{ $product->name }}? This cannot be undone.">
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="icon"><i class="bi bi-{{ request()->hasAny(['search', 'category_id', 'status']) ? 'search' : 'box-seam' }}"></i></div>
                                    @if (request()->hasAny(['search', 'category_id', 'status']))
                                        <div>No products match your search.</div>
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                            <i class="bi bi-x-lg me-1"></i>Clear filters
                                        </a>
                                    @else
                                        <div>No products found.</div>
                                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-dark mt-3">
                                            <i class="bi bi-plus-lg me-1"></i>Add your first product
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($products->hasPages())
        <div class="mt-3 small">
            {{ $products->onEachSide(1)->links() }}
        </div>
    @endif
    </div>{{-- /[data-search-results] --}}
@endsection
