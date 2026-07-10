@extends('layouts.app')

@section('title', $bo->type.' — '.$bo->date->format('M d, Y'))

@section('content')
    <a href="{{ route('bo.history') }}" class="text-decoration-none small text-secondary d-inline-block mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to records
    </a>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 page-title">
                {{ $bo->type }} — {{ $bo->date->format('M d, Y') }}
                @if ($bo->archived_at)
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis align-middle ms-1">
                        <i class="bi bi-archive me-1"></i>Archived
                    </span>
                @endif
            </h1>
            <p class="page-sub mb-0">
                Saved by {{ $bo->user->name ?? '—' }} on {{ $bo->created_at->format('M d, Y g:i A') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="bo-load" class="btn btn-outline-secondary">
                <i class="bi bi-pencil-square me-1"></i>Load into editor
            </button>
            <button type="button" id="bo-export" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export to Excel
            </button>
        </div>
    </div>

    <div class="card card-elevated overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-app">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>DR#</th>
                        <th>Store</th>
                        <th>Product</th>
                        <th class="text-end">QTY</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bo->items as $item)
                        <tr>
                            <td class="text-secondary">{{ $loop->iteration }}</td>
                            <td>{{ $item->dr ?: '—' }}</td>
                            <td>{{ $item->store ?: '—' }}</td>
                            <td class="fw-medium">{{ $item->product }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">₱{{ number_format($item->cost, 2) }}</td>
                            <td class="text-end fw-semibold">₱{{ number_format($item->total, 2) }}</td>
                            <td class="text-secondary">{{ $item->remarks ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold text-success">₱{{ number_format($bo->items->sum('total'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ===================== History (audit trail) ===================== --}}
    @php
        $actionMeta = [
            'created' => ['label' => 'Created', 'icon' => 'plus-circle', 'class' => 'text-success'],
            'updated' => ['label' => 'Edited', 'icon' => 'pencil', 'class' => 'text-primary'],
            'archived' => ['label' => 'Archived', 'icon' => 'archive', 'class' => 'text-secondary'],
            'restored' => ['label' => 'Restored', 'icon' => 'arrow-counterclockwise', 'class' => 'text-success'],
        ];
    @endphp
    <div class="card card-elevated overflow-hidden mt-4">
        <div class="card-header bg-body py-2">
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-1 text-secondary"></i>History</h2>
        </div>
        <ul class="list-group list-group-flush">
            @foreach ($bo->activities->sortByDesc('created_at')->sortByDesc('id') as $activity)
                @php $meta = $actionMeta[$activity->action] ?? ['label' => ucfirst($activity->action), 'icon' => 'dot', 'class' => 'text-secondary']; @endphp
                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-{{ $meta['icon'] }} {{ $meta['class'] }}"></i>
                    <span><strong>{{ $meta['label'] }}</strong> by {{ $activity->user_name ?? $activity->user->name ?? 'Unknown user' }}</span>
                    <span class="text-secondary small ms-auto">{{ $activity->created_at->format('M d, Y g:i A') }}</span>
                </li>
            @endforeach
            @unless ($bo->activities->contains('action', 'created'))
                {{-- Records saved before the audit trail existed. --}}
                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-plus-circle text-success"></i>
                    <span><strong>Created</strong> by {{ $bo->user->name ?? 'Unknown user' }}</span>
                    <span class="text-secondary small ms-auto">{{ $bo->created_at->format('M d, Y g:i A') }}</span>
                </li>
            @endunless
        </ul>
    </div>

    @include('partials.bo-export')

    <script>
        (function () {
            const BO_META = @json(['type' => $bo->type, 'date' => $bo->date->format('Y-m-d'), 'savedBy' => $bo->user->name ?? '']);
            const BO_ENTRIES = @json($entries);

            document.getElementById('bo-export').addEventListener('click', function () {
                window.exportBoToExcel(BO_META, BO_ENTRIES);
            });

            // Copy this sheet into the shared B.O editor draft (server-side).
            document.getElementById('bo-load').addEventListener('click', function () {
                const loadSheet = function () {
                    fetch(@json(route('bo.draft.load', $bo)), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Could not load the sheet. Please try again.');
                            return response.json();
                        })
                        .then(data => { window.location = data.redirect; })
                        .catch(error => Swal.fire({ icon: 'error', title: 'Load failed', text: error.message, confirmButtonColor: '#212529' }));
                };

                fetch(@json(route('bo.draft')), { headers: { 'Accept': 'application/json' } })
                    .then(response => response.ok ? response.json() : { entries: [] })
                    .then(payload => {
                        if (payload.entries && payload.entries.length) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Replace the current draft?',
                                text: `The shared B.O editor has ${payload.entries.length} unsaved item(s). Loading this sheet replaces them for everyone.`,
                                showCancelButton: true,
                                confirmButtonText: 'Replace',
                                confirmButtonColor: '#dc3545',
                            }).then(result => { if (result.isConfirmed) loadSheet(); });
                        } else {
                            loadSheet();
                        }
                    });
            });
        })();
    </script>
@endsection
