@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @auth
        <div class="mb-4">
            <h1 class="h3 mb-1 page-title">Welcome back, {{ auth()->user()->name }} 👋</h1>
            <p class="page-sub mb-0">{{ now()->format('l, F j, Y') }}</p>
        </div>

        {{-- ===================== Stats ===================== --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('products.index') }}" class="card card-elevated card-hover text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary-subtle text-primary-emphasis"><i class="bi bi-box-seam"></i></div>
                        <div>
                            <div class="fs-4 fw-bold lh-1 text-body">{{ $productCount }}</div>
                            <div class="text-secondary small">Active products</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('stores.index') }}" class="card card-elevated card-hover text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success-subtle text-success-emphasis"><i class="bi bi-shop"></i></div>
                        <div>
                            <div class="fs-4 fw-bold lh-1 text-body">{{ $storeCount }}</div>
                            <div class="text-secondary small">Stores</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('categories.index') }}" class="card card-elevated card-hover text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-tags"></i></div>
                        <div>
                            <div class="fs-4 fw-bold lh-1 text-body">{{ $categoryCount }}</div>
                            <div class="text-secondary small">Categories</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('bo.history') }}" class="card card-elevated card-hover text-decoration-none h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info-subtle text-info-emphasis"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <div class="fs-4 fw-bold lh-1 text-body">{{ $boCount }}</div>
                            <div class="text-secondary small">Saved B.O sheets</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            {{-- ===================== B.O draft ===================== --}}
            <div class="col-lg-7">
                <div class="card card-elevated h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <h2 class="h6 fw-bold mb-3">
                            <i class="bi bi-clipboard2-minus me-1 text-secondary"></i>B.O Sheet
                        </h2>

                        {{-- Shown when there is no saved draft (default). --}}
                        <div id="bo-draft-empty" class="my-auto text-center py-3">
                            <div class="empty-state py-2">
                                <div class="icon"><i class="bi bi-clipboard2-plus"></i></div>
                                <div class="mb-3">No unfinished B.O sheet.</div>
                                <a href="{{ route('bo.index') }}" class="btn btn-dark">
                                    <i class="bi bi-plus-lg me-1"></i>Start a new B.O
                                </a>
                            </div>
                        </div>

                        {{-- Shown by JS when a draft exists in localStorage. --}}
                        <div id="bo-draft-resume" class="my-auto d-none">
                            <p class="text-secondary small mb-1">You have an unfinished sheet:</p>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <span id="bo-draft-type" class="badge rounded-pill bg-primary-subtle text-primary-emphasis"></span>
                                <span id="bo-draft-date" class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis"></span>
                                <span id="bo-draft-summary" class="fw-bold"></span>
                            </div>
                            <a href="{{ route('bo.index') }}" class="btn btn-dark">
                                <i class="bi bi-arrow-right-circle me-1"></i>Continue B.O sheet
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== Quick actions ===================== --}}
            <div class="col-lg-5">
                <div class="card card-elevated h-100">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold mb-3">
                            <i class="bi bi-lightning-charge me-1 text-secondary"></i>Quick actions
                        </h2>
                        <div class="d-grid gap-2">
                            <a href="{{ route('bo.index') }}" class="btn btn-dark text-start">
                                <i class="bi bi-clipboard2-minus me-2"></i>New B.O sheet
                            </a>
                            <a href="{{ route('products.create') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-plus-lg me-2"></i>Add product
                            </a>
                            <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-shop me-2"></i>Manage stores
                            </a>
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-tags me-2"></i>Manage categories
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== Recent B.O records ===================== --}}
        <div class="card card-elevated overflow-hidden mb-4">
            <div class="card-header bg-body d-flex align-items-center py-2">
                <h2 class="h6 fw-bold mb-0 me-auto">
                    <i class="bi bi-clock-history me-1 text-secondary"></i>Recent B.O records
                </h2>
                <a href="{{ route('bo.history') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-app">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Items</th>
                            <th class="text-end">Total</th>
                            <th>Saved by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBos as $recentBo)
                            <tr role="button" onclick="window.location='{{ route('bo.show', $recentBo) }}'">
                                <td class="fw-medium">{{ $recentBo->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ [
                                        'B.O' => 'bg-primary-subtle text-primary-emphasis',
                                        'PULL-OUT' => 'bg-warning-subtle text-warning-emphasis',
                                        'RETURN' => 'bg-info-subtle text-info-emphasis',
                                    ][$recentBo->type] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">{{ $recentBo->type }}</span>
                                </td>
                                <td class="text-end">{{ $recentBo->items_count }}</td>
                                <td class="text-end fw-semibold">₱{{ number_format($recentBo->total ?? 0, 2) }}</td>
                                <td class="text-secondary">{{ $recentBo->user->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="icon"><i class="bi bi-clock-history"></i></div>
                                        <div>No saved sheets yet. Build one and hit <strong>Save BO</strong>.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================== Recent products ===================== --}}
        <div class="card card-elevated overflow-hidden">
            <div class="card-header bg-body d-flex align-items-center py-2">
                <h2 class="h6 fw-bold mb-0 me-auto">
                    <i class="bi bi-clock-history me-1 text-secondary"></i>Recently updated products
                </h2>
                <a href="{{ route('products.index') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-app">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentProducts as $product)
                            <tr>
                                <td class="fw-medium">{{ $product->name }}</td>
                                <td><span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis fw-medium">{{ $product->category->name }}</span></td>
                                <td class="text-end fw-semibold">₱{{ number_format($product->price, 2) }}</td>
                                <td class="text-end text-secondary small">{{ $product->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="icon"><i class="bi bi-box-seam"></i></div>
                                        <div>No products yet.</div>
                                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-dark mt-3">
                                            <i class="bi bi-plus-lg me-1"></i>Add your first product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Surface the shared in-progress B.O draft (server-side).
            (function () {
                fetch(@json(route('bo.draft')), { headers: { 'Accept': 'application/json' } })
                    .then(response => response.ok ? response.json() : null)
                    .then(payload => {
                        const entries = payload && Array.isArray(payload.entries) ? payload.entries : [];
                        if (!entries.length) return;

                        const total = entries.reduce((sum, e) => sum + (Number(e.qty) * Number(e.cost) || 0), 0);
                        document.getElementById('bo-draft-type').textContent = payload.meta.type || 'B.O';
                        document.getElementById('bo-draft-date').textContent = payload.meta.date || '';
                        document.getElementById('bo-draft-summary').textContent =
                            entries.length + ' item(s) — ₱' + total.toLocaleString('en-PH', {
                                minimumFractionDigits: 2, maximumFractionDigits: 2,
                            });
                        document.getElementById('bo-draft-empty').classList.add('d-none');
                        document.getElementById('bo-draft-resume').classList.remove('d-none');
                    })
                    .catch(() => { /* keep the "start new" state */ });
            })();
        </script>
    @else
        {{-- ===================== Guest landing ===================== --}}
        <div class="text-center pb-4 hero-pull-up pt-3">
            <img src="{{ asset_versioned('logo/hero.png') }}" alt="{{ config('app.name') }} logo" class="mb-2"
                style="width: 23rem; height: auto;">
            <h1 class="display-6 fw-bold page-title mb-2">Pricelists &amp; B.O sheets, without the spreadsheets</h1>
            <p class="text-secondary mx-auto mb-3" style="max-width: 560px;">
                {{ config('app.name') }} keeps your product pricelist and store branches in one place,
                so building a B.O, pull-out, or return sheet takes minutes — not a morning.
            </p>
            <div class="d-flex justify-content-center gap-2 mb-4">
                <a href="{{ route('login') }}" class="btn btn-dark px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Sign in
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary px-4">
                    Create an account
                </a>
            </div>

            <div class="row g-3 text-start mx-auto" style="max-width: 960px;">
                <div class="col-md-4">
                    <div class="card card-elevated h-100">
                        <div class="card-body p-3">
                            <div class="stat-icon bg-primary-subtle text-primary-emphasis mb-3"><i class="bi bi-box-seam"></i></div>
                            <h2 class="h6 fw-bold">Full pricelist, always current</h2>
                            <p class="text-secondary small mb-0">
                                Every product organized by category with its SRP — searchable and filterable, updated in one place.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-elevated h-100">
                        <div class="card-body p-3">
                            <div class="stat-icon bg-success-subtle text-success-emphasis mb-3"><i class="bi bi-shop"></i></div>
                            <h2 class="h6 fw-bold">All your branches</h2>
                            <p class="text-secondary small mb-0">
                                The complete store list with branch codes, ready to drop into any sheet — no more memorizing codes.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-elevated h-100">
                        <div class="card-body p-3">
                            <div class="stat-icon bg-warning-subtle text-warning-emphasis mb-3"><i class="bi bi-file-earmark-excel"></i></div>
                            <h2 class="h6 fw-bold">Click, tally, export</h2>
                            <p class="text-secondary small mb-0">
                                Build B.O, pull-out, or return sheets by clicking products, then export straight to Excel.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endauth
@endsection
