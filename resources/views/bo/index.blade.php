@extends('layouts.app')

@section('title', 'B.O')

@section('main_class', 'container-fluid px-3 px-lg-4')

@section('content')
    {{-- ===================== Header toolbar ===================== --}}
    <div class="card card-elevated mb-3">
        <div class="card-body py-3 d-flex flex-wrap align-items-center gap-3">
            <label class="d-flex align-items-center gap-2 mb-0 text-nowrap">
                <span class="label-caps">BO Type</span>
                <select id="bo-type" class="form-select form-select-sm" style="width: 200px;">
                    <option value="B.O">B.O</option>
                    <option value="PULL-OUT">PULL-OUT</option>
                    <option value="RETURN">RETURN</option>
                </select>
            </label>
            <label class="d-flex align-items-center gap-2 mb-0 text-nowrap">
                <span class="label-caps">Date</span>
                <input type="date" id="bo-date" class="form-control form-control-sm" style="width: 160px;"
                    value="{{ now()->format('Y-m-d') }}">
            </label>
            <label class="d-flex align-items-center gap-2 mb-0 text-nowrap">
                <span class="label-caps">DR#</span>
                <input type="text" id="bo-dr" class="form-control form-control-sm" style="width: 120px;">
            </label>
            <label class="d-flex align-items-center gap-2 mb-0 text-nowrap">
                <span class="label-caps">Store</span>
                <input type="text" id="bo-store" class="form-control form-control-sm" style="width: 180px;" list="store-list">
            </label>

            <datalist id="store-list">
                @foreach ($stores as $store)
                    <option value="{{ $store->name }}">{{ $store->code }}</option>
                @endforeach
            </datalist>

            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('remarks.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chat-left-text me-1"></i>Remarks
                </a>
                <a href="{{ route('bo.history') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-clock-history me-1"></i>Records
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ===================== Pricelist (left) ===================== --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card card-elevated overflow-hidden">
                <div class="card-header bg-body d-flex align-items-center gap-2 py-2">
                    <h2 class="h6 fw-bold mb-0 me-auto text-nowrap">
                        <i class="bi bi-tags me-1 text-secondary"></i>Pricelist
                    </h2>
                    <input type="search" id="pl-search" class="form-control form-control-sm"
                        style="max-width: 220px;" placeholder="Search products…">
                </div>
                <div style="max-height: 65vh; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0 table-app">
                        <thead style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 90px;">Category</th>
                                <th>Product</th>
                                <th class="text-end pe-3" style="width: 110px;">Price</th>
                            </tr>
                        </thead>
                        <tbody id="pl-body">
                            @php $currentCategory = null; @endphp
                            @forelse ($products as $product)
                                @if ($product->category->name !== $currentCategory)
                                    @php $currentCategory = $product->category->name; @endphp
                                    <tr class="pl-category" data-category="{{ $currentCategory }}">
                                        <td></td>
                                        <td colspan="2" class="fw-bold text-primary">{{ $currentCategory }}</td>
                                    </tr>
                                @endif
                                <tr class="pl-product" role="button" title="Add to BO"
                                    data-id="{{ $product->id }}"
                                    data-category="{{ $currentCategory }}"
                                    data-name="{{ Str::lower($product->name) }}">
                                    <td></td>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end pe-3">₱{{ number_format($product->price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        No products yet. <a href="{{ route('products.create') }}">Add products</a> first.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="pl-no-results" class="d-none">
                                <td colspan="3" class="text-center text-secondary py-4">No products match.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="small text-secondary mt-2 mb-0">
                <i class="bi bi-hand-index me-1"></i>Click a product to add it to the BO.
            </p>
        </div>

        {{-- ===================== BO Entries (right) ===================== --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card card-elevated overflow-hidden h-100">
                <div class="card-header bg-body d-flex align-items-center gap-2 py-2" style="min-height: 49px;">
                    <h2 class="h6 fw-bold mb-0">
                        <i class="bi bi-clipboard2-minus me-1 text-secondary"></i>BO Entries
                    </h2>
                    <span id="bo-editors" class="small text-primary me-auto"></span>
                    <span id="bo-total" class="fw-bold text-success small"></span>
                </div>
                {{-- flex-grow fills the card so this column matches the pricelist height. --}}
                <div class="table-responsive flex-grow-1" style="min-height: 0; max-height: 65vh; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0 table-app">
                        <thead style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>DR#</th>
                                <th>Store</th>
                                <th>Product</th>
                                <th class="text-end">QTY</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Total</th>
                                <th>Remarks</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="bo-body"></tbody>
                    </table>
                </div>
                <div class="card-footer bg-body d-flex align-items-center gap-2 py-2">
                    <span class="small text-secondary me-auto d-none d-sm-inline">
                        <i class="bi bi-mouse2 me-1"></i>Double-click an entry to edit it
                    </span>
                    <button type="button" id="bo-clear" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-trash3 me-1"></i>Clear All
                    </button>
                    <button type="button" id="bo-export" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export to Excel
                    </button>
                    <button type="button" id="bo-save" class="btn btn-sm btn-dark">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Save BO
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Add / edit item modal ===================== --}}
    <div class="modal fade" id="addItemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow rounded-4" id="addItemForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="addItemModalLabel">Add Item to BO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="small text-secondary">Product</div>
                        <div id="m-product" class="fw-bold text-primary"></div>
                    </div>
                    <div class="mb-3">
                        <label for="m-dr" class="form-label small mb-1">DR# <span class="text-danger">*</span></label>
                        <input type="text" id="m-dr" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="m-store" class="form-label small mb-1">STORE <span class="text-danger">*</span></label>
                        <input type="text" id="m-store" class="form-control" list="store-list" required>
                    </div>
                    <div class="mb-3">
                        <label for="m-qty" class="form-label small mb-1">Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="m-qty" class="form-control" min="1" step="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="m-cost" class="form-label small mb-1">Unit Cost (₱) <span class="text-danger">*</span></label>
                        <input type="number" id="m-cost" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="m-remarks" class="form-label small mb-1">Remarks <span class="text-danger">*</span></label>
                        <select id="m-remarks" class="form-select" required>
                            <option value="">— Select a remark —</option>
                            @foreach ($remarks as $remark)
                                <option value="{{ $remark->name }}">{{ $remark->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.bo-export')

    <script>
        // Deferred: the Bootstrap bundle loads at the end of <body> in layouts/app,
        // so bootstrap.Modal isn't available while this inline script is parsed.
        window.addEventListener('DOMContentLoaded', function () {
            const URLS = {
                draft: @json(route('bo.draft')),
                meta: @json(route('bo.draft.meta')),
                entries: @json(route('bo.draft.entries.add')),
                clear: @json(route('bo.draft.entries.clear')),
                save: @json(route('bo.draft.save')),
            };
            const PRODUCTS = @json($products->mapWithKeys(fn ($p) => [$p->id => [
                'name' => $p->name,
                'price' => (float) $p->price,
            ]]));

            const el = id => document.getElementById(id);
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
            }[c]));
            const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2, maximumFractionDigits: 2,
            });

            // ---- shared server draft ---------------------------------------------
            // The draft lives on the server so several users can edit together;
            // this page polls for changes and pushes every mutation.
            let entries = [];
            let meta = { type: 'B.O', date: '', dr: '', store: '', boId: null };

            function api(url, method, body) {
                return fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: body ? JSON.stringify(body) : undefined,
                }).then(async response => {
                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data.message || 'Request failed. Please try again.');
                    }
                    return response.json();
                });
            }

            function applyPayload(payload) {
                entries = payload.entries;
                meta = payload.meta;

                // Sync header fields, but never yank one out from under the user.
                [['bo-type', 'type'], ['bo-date', 'date'], ['bo-dr', 'dr'], ['bo-store', 'store']].forEach(([id, key]) => {
                    const input = el(id);
                    if (document.activeElement !== input) input.value = meta[key] ?? '';
                });

                renderEntries();
                renderEditors(payload.editors);
            }

            function refresh() {
                api(URLS.draft + '?presence=1', 'GET').then(applyPayload).catch(() => { /* offline blip — retry next poll */ });
            }
            refresh();
            setInterval(refresh, 4000);

            function renderEditors(editors) {
                const span = el('bo-editors');
                span.innerHTML = (editors && editors.length)
                    ? '<i class="bi bi-people-fill me-1"></i>Also editing: ' + editors.map(esc).join(', ')
                    : '';
            }

            // ---- header fields (shared meta) --------------------------------------
            let metaTimer;
            function pushMeta() {
                clearTimeout(metaTimer);
                metaTimer = setTimeout(() => {
                    api(URLS.meta, 'PATCH', {
                        type: el('bo-type').value,
                        date: el('bo-date').value,
                        dr: el('bo-dr').value,
                        store: el('bo-store').value,
                    }).then(applyPayload).catch(() => {});
                }, 400);
            }
            el('bo-type').addEventListener('change', pushMeta);
            el('bo-date').addEventListener('change', pushMeta);
            el('bo-dr').addEventListener('input', pushMeta);
            el('bo-store').addEventListener('input', pushMeta);

            // ---- pricelist filtering (search) ------------------------------------
            function filterPricelist() {
                const q = el('pl-search').value.trim().toLowerCase();
                let anyVisible = false;

                document.querySelectorAll('#pl-body .pl-product').forEach(row => {
                    const show = !q || row.dataset.name.includes(q);
                    row.classList.toggle('d-none', !show);
                    if (show) anyVisible = true;
                });

                document.querySelectorAll('#pl-body .pl-category').forEach(row => {
                    const show = document.querySelector(
                        `#pl-body .pl-product[data-category="${CSS.escape(row.dataset.category)}"]:not(.d-none)`
                    ) !== null;
                    row.classList.toggle('d-none', !show);
                });

                const empty = el('pl-no-results');
                if (empty) empty.classList.toggle('d-none', anyVisible || !Object.keys(PRODUCTS).length);
            }
            el('pl-search').addEventListener('input', filterPricelist);

            // ---- add / edit item modal -------------------------------------------
            const modalEl = el('addItemModal');
            const modal = new bootstrap.Modal(modalEl);
            const modalTitle = el('addItemModalLabel');
            const modalSubmit = document.querySelector('#addItemForm button[type="submit"]');
            let editingId = null; // null = adding, otherwise the draft entry id being edited

            // Select a remark, adding a temporary option for values no longer in
            // the managed list (deleted remark / older free-text entries).
            function setRemark(value) {
                const select = el('m-remarks');
                if (value && ![...select.options].some(option => option.value === value)) {
                    select.add(new Option(value, value));
                }
                select.value = value || '';
            }

            document.querySelectorAll('#pl-body .pl-product').forEach(row => {
                row.addEventListener('click', () => {
                    const product = PRODUCTS[row.dataset.id];
                    if (!product) return;
                    editingId = null;
                    modalTitle.textContent = 'Add Item to BO';
                    modalSubmit.textContent = 'Confirm';
                    el('m-product').textContent = product.name;
                    el('m-dr').value = el('bo-dr').value;
                    el('m-store').value = el('bo-store').value;
                    el('m-qty').value = 1;
                    el('m-cost').value = product.price.toFixed(2);
                    setRemark('');
                    modal.show();
                });
            });

            // Double-click an entry row to edit it in the same modal.
            el('bo-body').addEventListener('dblclick', e => {
                if (e.target.closest('[data-remove]')) return;
                const row = e.target.closest('tr[data-id]');
                if (!row) return;
                const entry = entries.find(item => item.id === Number(row.dataset.id));
                if (!entry) return;
                editingId = entry.id;
                modalTitle.textContent = 'Edit BO Entry';
                modalSubmit.textContent = 'Save changes';
                el('m-product').textContent = entry.product;
                el('m-dr').value = entry.dr;
                el('m-store').value = entry.store;
                el('m-qty').value = entry.qty;
                el('m-cost').value = Number(entry.cost).toFixed(2);
                setRemark(entry.remarks);
                modal.show();
            });

            modalEl.addEventListener('shown.bs.modal', () => el('m-qty').select());
            modalEl.addEventListener('hidden.bs.modal', () => { editingId = null; });

            el('addItemForm').addEventListener('submit', e => {
                e.preventDefault();
                const qty = Number(el('m-qty').value);
                const cost = Number(el('m-cost').value);
                if (!(qty > 0) || !(cost >= 0)) return;

                const entry = {
                    dr: el('m-dr').value.trim(),
                    store: el('m-store').value.trim(),
                    product: el('m-product').textContent,
                    qty: qty,
                    cost: cost,
                    remarks: el('m-remarks').value.trim(),
                };

                const request = editingId !== null
                    ? api(URLS.entries + '/' + editingId, 'PUT', entry)
                    : api(URLS.entries, 'POST', entry);
                const wasAdding = editingId === null;

                request
                    .then(payload => {
                        applyPayload(payload);
                        modal.hide();
                        toast('success', wasAdding
                            ? (payload.merged
                                ? `${entry.product} already listed — quantity increased by ${entry.qty}.`
                                : `${entry.product} added.`)
                            : `${entry.product} updated.`);
                        // Carry DR#/STORE back as shared defaults for the next item.
                        if (wasAdding && (entry.dr !== meta.dr || entry.store !== meta.store)) {
                            api(URLS.meta, 'PATCH', { dr: entry.dr, store: entry.store }).then(applyPayload).catch(() => {});
                        }
                    })
                    .catch(error => toast('error', error.message, { timer: 5000 }));
            });

            // ---- entries table ----------------------------------------------------
            function renderEntries() {
                const body = el('bo-body');
                if (!entries.length) {
                    body.innerHTML = '<tr><td colspan="9" class="align-middle" style="height: 50vh;">'
                        + '<div class="empty-state">'
                        + '<div class="icon"><i class="bi bi-clipboard2-x"></i></div>'
                        + '<div>No entries yet. Click a product on the left to add one.</div>'
                        + '</div></td></tr>';
                } else {
                    body.innerHTML = entries.map((entry, i) => `
                        <tr data-id="${entry.id}" title="Double-click to edit">
                            <td class="text-secondary">${i + 1}</td>
                            <td>${esc(entry.dr) || '—'}</td>
                            <td>${esc(entry.store) || '—'}</td>
                            <td>
                                ${esc(entry.product)}
                                ${entry.added_by ? `<div class="small text-secondary"><i class="bi bi-person me-1"></i>added by ${esc(entry.added_by)}</div>` : ''}
                            </td>
                            <td class="text-end">${entry.qty}</td>
                            <td class="text-end">${peso(entry.cost)}</td>
                            <td class="text-end">${peso(entry.qty * entry.cost)}</td>
                            <td class="text-secondary">${esc(entry.remarks) || '—'}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-1"
                                    data-remove="${entry.id}" title="Remove">&times;</button>
                            </td>
                        </tr>`).join('');
                }

                const total = entries.reduce((sum, entry) => sum + entry.qty * entry.cost, 0);
                el('bo-total').textContent = `Total: ${peso(total)} | ${entries.length} item(s)`;
            }

            el('bo-body').addEventListener('click', e => {
                const btn = e.target.closest('[data-remove]');
                if (!btn) return;
                const entry = entries.find(item => item.id === Number(btn.dataset.remove));
                if (!entry) return;
                Swal.fire({
                    icon: 'warning',
                    title: 'Remove this entry?',
                    text: `${entry.product} (QTY ${entry.qty}) will be removed from the BO.`,
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    confirmButtonColor: '#dc3545',
                }).then(result => {
                    if (!result.isConfirmed) return;
                    api(URLS.entries + '/' + entry.id, 'DELETE')
                        .then(payload => {
                            applyPayload(payload);
                            toast('success', `${entry.product} removed.`);
                        })
                        .catch(error => toast('error', error.message, { timer: 5000 }));
                });
            });

            el('bo-clear').addEventListener('click', () => {
                if (!entries.length) return;
                Swal.fire({
                    icon: 'warning',
                    title: 'Clear all entries?',
                    text: 'This removes every BO entry for everyone editing this sheet.',
                    showCancelButton: true,
                    confirmButtonText: 'Clear All',
                    confirmButtonColor: '#dc3545',
                }).then(result => {
                    if (!result.isConfirmed) return;
                    api(URLS.clear, 'DELETE')
                        .then(payload => {
                            applyPayload(payload);
                            toast('success', 'All entries cleared.');
                        })
                        .catch(error => toast('error', error.message, { timer: 5000 }));
                });
            });

            // ---- export to Excel (shared styled exporter in partials/bo-export) ---
            el('bo-export').addEventListener('click', () => {
                window.exportBoToExcel(
                    { type: meta.type, date: meta.date, savedBy: @json(auth()->user()->name) },
                    entries,
                );
            });

            // ---- save to records ---------------------------------------------------
            el('bo-save').addEventListener('click', () => {
                if (!entries.length) {
                    toast('info', 'Nothing to save — add at least one BO entry first.');
                    return;
                }

                const total = entries.reduce((sum, entry) => sum + entry.qty * entry.cost, 0);
                const summary = `${entries.length} item(s) — ${peso(total)}`;

                const doSave = mode => {
                    api(URLS.save, 'POST', { mode: mode })
                        .then(data => { window.location = data.redirect; })
                        .catch(error => toast('error', error.message, { timer: 5000 }));
                };

                if (meta.boId) {
                    // Sheet was loaded from a saved record: update it or save a copy.
                    Swal.fire({
                        icon: 'question',
                        title: 'Save changes?',
                        text: `${summary}. This sheet was loaded from records — update the original or save as a new record?`,
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Update record',
                        denyButtonText: 'Save as new',
                        confirmButtonColor: '#212529',
                        denyButtonColor: '#6c757d',
                    }).then(result => {
                        if (result.isConfirmed) doSave('update');
                        else if (result.isDenied) doSave('new');
                    });
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Save this BO sheet?',
                        text: `${summary} will be saved to records.`,
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        confirmButtonColor: '#212529',
                    }).then(result => {
                        if (result.isConfirmed) doSave('new');
                    });
                }
            });

            renderEntries();
        });
    </script>
@endsection
