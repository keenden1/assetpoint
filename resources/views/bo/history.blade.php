@extends('layouts.app')

@section('title', 'Records')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 page-title">Records</h1>
            <p class="page-sub mb-0">Every saved sheet — open one to view, export, or reload it.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="bo-export-selected" class="btn btn-success" disabled>
                <i class="bi bi-file-earmark-excel me-1"></i>Export selected (<span id="sel-count">0</span>)
            </button>
            <a href="{{ route('bo.index') }}" class="btn btn-dark">
                <i class="bi bi-plus-lg me-1"></i>New B.O sheet
            </a>
        </div>
    </div>

    <div class="d-flex gap-1 mb-3">
        <a href="{{ route('bo.history') }}" class="nav-pill @unless ($showArchived) active @endunless">
            <i class="bi bi-collection me-1"></i>Active ({{ $activeCount }})
        </a>
        <a href="{{ route('bo.history', ['archived' => 1]) }}" class="nav-pill @if ($showArchived) active @endif">
            <i class="bi bi-archive me-1"></i>Archived ({{ $archivedCount }})
        </a>
    </div>

    <div class="card card-elevated overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-app">
                <thead>
                    <tr>
                        <th style="width: 36px;">
                            <input type="checkbox" id="select-all" class="form-check-input" title="Select all on this page">
                        </th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Items</th>
                        <th class="text-end">Total</th>
                        <th>Saved by</th>
                        <th>Saved at</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bos as $bo)
                        <tr role="button" onclick="window.location='{{ route('bo.show', $bo) }}'"
                            @if ($showArchived) class="row-archived" @endif>
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" class="form-check-input bo-select" value="{{ $bo->id }}">
                            </td>
                            <td class="fw-medium">{{ $bo->date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge rounded-pill {{ [
                                    'B.O' => 'bg-primary-subtle text-primary-emphasis',
                                    'PULL-OUT' => 'bg-warning-subtle text-warning-emphasis',
                                    'RETURN' => 'bg-info-subtle text-info-emphasis',
                                ][$bo->type] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">{{ $bo->type }}</span>
                            </td>
                            <td class="text-end">{{ $bo->items_count }}</td>
                            <td class="text-end fw-semibold">₱{{ number_format($bo->total ?? 0, 2) }}</td>
                            <td class="text-secondary">{{ $bo->user->name ?? '—' }}</td>
                            <td class="text-secondary small">{{ $bo->created_at->format('M d, Y g:i A') }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bo.show', $bo) }}" class="btn btn-sm btn-outline-secondary border-0" title="View" onclick="event.stopPropagation()">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if (auth()->user()->is_admin || $bo->user_id === auth()->id())
                                    @if ($showArchived)
                                        <form method="POST" action="{{ route('bo.unarchive', $bo) }}" class="d-inline" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success border-0" title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('bo.archive', $bo) }}" class="d-inline" onclick="event.stopPropagation()"
                                            data-confirm="Archive the {{ $bo->type }} sheet from {{ $bo->date->format('M d, Y') }}? You can restore it from the Archived tab."
                                            data-confirm-button="Archive" data-confirm-color="#212529">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary border-0" title="Archive">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="icon"><i class="bi bi-{{ $showArchived ? 'archive' : 'clock-history' }}"></i></div>
                                    @if ($showArchived)
                                        <div>No archived sheets.</div>
                                    @else
                                        <div>No saved sheets yet. Build one and hit <strong>Save BO</strong>.</div>
                                        <a href="{{ route('bo.index') }}" class="btn btn-sm btn-dark mt-3">
                                            <i class="bi bi-plus-lg me-1"></i>Start a B.O sheet
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

    @if ($bos->hasPages())
        <div class="mt-3 small">
            {{ $bos->onEachSide(1)->links() }}
        </div>
    @endif

    @include('partials.bo-export')

    <script>
        (function () {
            const selectAll = document.getElementById('select-all');
            const boxes = Array.from(document.querySelectorAll('.bo-select'));
            const btn = document.getElementById('bo-export-selected');
            const count = document.getElementById('sel-count');

            function sync() {
                const n = boxes.filter(b => b.checked).length;
                count.textContent = n;
                btn.disabled = !n;
                if (selectAll) selectAll.checked = n > 0 && n === boxes.length;
            }
            boxes.forEach(b => b.addEventListener('change', sync));
            if (selectAll) selectAll.addEventListener('change', () => {
                boxes.forEach(b => { b.checked = selectAll.checked; });
                sync();
            });
            sync();

            btn.addEventListener('click', () => {
                const ids = boxes.filter(b => b.checked).map(b => b.value);
                if (!ids.length) return;

                const url = new URL(@json(route('bo.export')), window.location.origin);
                ids.forEach(id => url.searchParams.append('ids[]', id));

                btn.disabled = true;
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(response => {
                        if (!response.ok) throw new Error('Could not load the selected records.');
                        return response.json();
                    })
                    .then(list => window.exportBosToExcel(list))
                    .catch(error => Swal.fire({ icon: 'error', title: 'Export failed', text: error.message, confirmButtonColor: '#212529' }))
                    .finally(sync);
            });
        })();
    </script>
@endsection
