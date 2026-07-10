@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 page-title">Audit Log</h1>
            <p class="page-sub mb-0">Every action in the system — who did what, and when.</p>
        </div>
        @if ($prunableCount)
            <form method="POST" action="{{ route('audit.prune') }}" id="prune-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="password">
                <button type="button" id="prune-btn" class="btn btn-outline-danger">
                    <i class="bi bi-trash3 me-1"></i>Delete logs older than 2 months ({{ $prunableCount }})
                </button>
            </form>
        @else
            <button type="button" class="btn btn-outline-secondary" disabled
                title="Nothing older than {{ $pruneCutoff->format('M d, Y') }}">
                <i class="bi bi-trash3 me-1"></i>Delete logs older than 2 months (0)
            </button>
        @endif
    </div>

    <form method="GET" action="{{ route('audit.index') }}" class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
            <input type="search" name="search" id="audit-search" data-live-search
                value="{{ request('search') }}" class="form-control" placeholder="Search user, action, or details">
            @if (request('search'))
                <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary" title="Clear search">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="card card-elevated mb-3">
        <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
            <span class="label-caps">Export range</span>
            <input type="date" id="exp-from" class="form-control form-control-sm" style="width: 160px;">
            <span class="text-secondary small">to</span>
            <input type="date" id="exp-to" class="form-control form-control-sm" style="width: 160px;">
            <button type="button" id="audit-export" class="btn btn-sm btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export to Excel
            </button>
            <span class="text-secondary small">Leave empty to export everything.</span>
        </div>
    </div>

    @php
        $actionColors = [
            'auth' => 'bg-secondary-subtle text-secondary-emphasis',
            'user' => 'bg-primary-subtle text-primary-emphasis',
            'product' => 'bg-success-subtle text-success-emphasis',
            'store' => 'bg-info-subtle text-info-emphasis',
            'category' => 'bg-warning-subtle text-warning-emphasis',
            'remark' => 'bg-warning-subtle text-warning-emphasis',
            'bo' => 'bg-danger-subtle text-danger-emphasis',
        ];
    @endphp

    <div data-search-results>
    <div class="card card-elevated overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-app">
                <thead>
                    <tr>
                        <th style="width: 190px;">When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-secondary small text-nowrap" title="{{ $log->created_at->diffForHumans() }}">
                                {{ $log->created_at->format('M d, Y g:i A') }}
                            </td>
                            <td class="fw-medium">{{ $log->user_name ?? 'System' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $actionColors[explode('.', $log->action)[0]] ?? 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="text-secondary">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="icon"><i class="bi bi-{{ request('search') ? 'search' : 'journal-text' }}"></i></div>
                                    @if (request('search'))
                                        <div>No log entries match "{{ request('search') }}".</div>
                                        <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                                            <i class="bi bi-x-lg me-1"></i>Clear search
                                        </a>
                                    @else
                                        <div>No activity logged yet.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($logs->hasPages())
        <div class="mt-3 small">
            {{ $logs->onEachSide(1)->links() }}
        </div>
    @endif
    </div>{{-- /[data-search-results] --}}

    {{-- xlsx-js-style: styled .xlsx generation, client-side. --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>

    <script>
        // Export the log (optionally date-ranged) as a styled Excel file.
        document.getElementById('audit-export').addEventListener('click', function () {
            const from = document.getElementById('exp-from').value;
            const to = document.getElementById('exp-to').value;
            if (from && to && from > to) {
                toast('error', 'The "from" date must be before the "to" date.');
                return;
            }

            const url = new URL(@json(route('audit.export')), window.location.origin);
            if (from) url.searchParams.set('from', from);
            if (to) url.searchParams.set('to', to);

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(response => {
                    if (!response.ok) throw new Error('Could not load the log entries.');
                    return response.json();
                })
                .then(logs => {
                    if (!logs.length) {
                        toast('info', 'No log entries in that date range.');
                        return;
                    }
                    if (typeof XLSX === 'undefined') {
                        toast('error', 'Export unavailable — the Excel library failed to load. Refresh and try again.', { timer: 6000 });
                        return;
                    }

                    const rows = [
                        ['When', 'User', 'Action', 'Details'],
                        ...logs.map(log => [log.when, log.user, log.action, log.description]),
                    ];
                    const ws = XLSX.utils.aoa_to_sheet(rows);
                    ws['!cols'] = [{ wch: 22 }, { wch: 18 }, { wch: 18 }, { wch: 70 }];
                    ws['!rows'] = rows.map((row, r) => r === 0 ? { hpt: 26 } : { hpt: 18 });

                    const cell = (r, c) => ws[XLSX.utils.encode_cell({ r: r, c: c })];
                    const thin = { style: 'thin', color: { rgb: 'D0D5DB' } };
                    const borders = { top: thin, bottom: thin, left: thin, right: thin };

                    for (let c = 0; c < 4; c++) {
                        const cel = cell(0, c);
                        if (cel) cel.s = {
                            font: { bold: true, color: { rgb: 'FFFFFF' } },
                            fill: { patternType: 'solid', fgColor: { rgb: '212529' } },
                            alignment: { horizontal: 'center', vertical: 'center' },
                            border: borders,
                        };
                    }
                    for (let r = 1; r < rows.length; r++) {
                        for (let c = 0; c < 4; c++) {
                            const cel = cell(r, c);
                            if (!cel) continue;
                            cel.s = Object.assign(
                                { border: borders, alignment: { vertical: 'center' } },
                                r % 2 === 1 ? { fill: { patternType: 'solid', fgColor: { rgb: 'F2F4F6' } } } : {});
                        }
                    }

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Audit Log');
                    XLSX.writeFile(wb, `Audit_${from || 'start'}_to_${to || new Date().toISOString().slice(0, 10)}.xlsx`);
                })
                .catch(error => toast('error', error.message, { timer: 5000 }));
        });

        // Pruning requires the admin to re-type their password.
        (function () {
            const btn = document.getElementById('prune-btn');
            if (!btn) return;
            btn.addEventListener('click', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete old logs?',
                    text: @json("{$prunableCount} log entries older than {$pruneCutoff->format('M d, Y')} will be permanently deleted. Enter your password to confirm."),
                    input: 'password',
                    inputPlaceholder: 'Your password',
                    inputAttributes: { autocomplete: 'current-password' },
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#dc3545',
                    preConfirm: function (value) {
                        if (!value) {
                            Swal.showValidationMessage('Password is required.');
                            return false;
                        }
                        return value;
                    },
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    const form = document.getElementById('prune-form');
                    form.querySelector('input[name="password"]').value = result.value;
                    form.submit();
                });
            });
        })();
    </script>
@endsection
