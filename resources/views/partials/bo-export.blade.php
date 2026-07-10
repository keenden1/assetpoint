{{-- Shared styled .xlsx export for BO sheets (editor + saved records views).
     Exposes:
       window.exportBoToExcel(meta, entries)  — single-sheet workbook
       window.exportBosToExcel(list)          — one sheet per record; list = [{ type, date, entries }, ...]
     meta    = { type: 'B.O', date: 'YYYY-MM-DD' }
     entries = [{ dr, store, product, qty, cost, remarks }, ...] --}}

{{-- xlsx-js-style: SheetJS fork that supports cell styling (colors, bold, number formats). --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>

<script>
(function () {
    const num = v => Number(v) || 0;
    // "2026-07-09" -> "Jul 09, 2026"
    const prettyDate = iso => iso
        ? new Date(iso + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        : '';

    function guard(entries) {
        if (!entries || !entries.length) {
            toast('info', 'Nothing to export — add at least one BO entry first.');
            return false;
        }
        if (typeof XLSX === 'undefined') {
            toast('error', 'Export unavailable — the Excel library failed to load. Refresh and try again.', { timer: 6000 });
            return false;
        }
        return true;
    }

    // Build one fully styled worksheet for a BO sheet:
    // Date | DR# | Store | Product | QTY | Unit Cost | Total | Remarks
    function buildBoSheet(meta, entries) {
        const total = entries.reduce((sum, entry) => sum + num(entry.qty) * num(entry.cost), 0);
        const dateLabel = prettyDate(meta.date);

        const headerRow = 0;
        const rows = [
            ['Date', 'DR#', 'Store', 'Product', 'QTY', 'Unit Cost', 'Total', 'Remarks'],
            ...entries.map(entry => [
                dateLabel, entry.dr, entry.store, entry.product,
                num(entry.qty), num(entry.cost), num(entry.qty) * num(entry.cost), entry.remarks,
            ]),
            ['', '', '', '', '', 'Total', total, ''],
        ];

        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws['!cols'] = [
            { wch: 14 }, { wch: 12 }, { wch: 20 }, { wch: 36 },
            { wch: 8 }, { wch: 12 }, { wch: 14 }, { wch: 26 },
        ];
        // Row heights: taller header, comfortable entry rows.
        ws['!rows'] = rows.map((row, r) => r === headerRow ? { hpt: 26 } : { hpt: 20 });

        // ---- styling ------------------------------------------------------
        const cell = (r, c) => ws[XLSX.utils.encode_cell({ r: r, c: c })];
        const totalRow = rows.length - 1;
        const thin = { style: 'thin', color: { rgb: 'D0D5DB' } };
        const borders = { top: thin, bottom: thin, left: thin, right: thin };

        // Column headers: dark fill, white bold text.
        for (let c = 0; c < 8; c++) {
            const cel = cell(headerRow, c);
            if (cel) cel.s = {
                font: { bold: true, color: { rgb: 'FFFFFF' } },
                fill: { patternType: 'solid', fgColor: { rgb: '212529' } },
                alignment: { horizontal: 'center', vertical: 'center' },
                border: borders,
            };
        }

        // Entry rows: borders, zebra striping, peso format on money columns.
        for (let r = headerRow + 1; r < totalRow; r++) {
            for (let c = 0; c < 8; c++) {
                const cel = cell(r, c);
                if (!cel) continue;
                cel.s = Object.assign(
                    { border: borders, alignment: { vertical: 'center' } },
                    (r - headerRow) % 2 === 0
                        ? { fill: { patternType: 'solid', fgColor: { rgb: 'F2F4F6' } } }
                        : {});
                if (c === 5 || c === 6) cel.z = '#,##0.00';
            }
        }

        // Total row: bold on a soft green highlight.
        [5, 6].forEach(c => {
            const cel = cell(totalRow, c);
            if (!cel) return;
            cel.s = {
                font: { bold: true },
                fill: { patternType: 'solid', fgColor: { rgb: 'D1E7DD' } },
                border: borders,
                alignment: { vertical: 'center' },
            };
            if (c === 6) cel.z = '#,##0.00';
        });

        return ws;
    }

    // Excel sheet names: max 31 chars, no : \ / ? * [ ], unique per workbook.
    function sheetName(meta, used) {
        let base = `${meta.type} ${meta.date}`.replace(/[:\\\/?*\[\]]/g, '-').slice(0, 28);
        let name = base;
        let n = 2;
        while (used.has(name)) name = `${base} (${n++})`;
        used.add(name);
        return name;
    }

    window.exportBoToExcel = function (meta, entries) {
        if (!guard(entries)) return;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, buildBoSheet(meta, entries), meta.type || 'BO');
        XLSX.writeFile(wb, `BO_${meta.date || 'export'}.xlsx`);
    };

    window.exportBosToExcel = function (list) {
        if (!list || !list.length || !guard(list[0].entries)) return;

        const wb = XLSX.utils.book_new();
        const used = new Set();
        list.forEach(bo => {
            XLSX.utils.book_append_sheet(wb, buildBoSheet(bo, bo.entries), sheetName(bo, used));
        });
        XLSX.writeFile(wb, `BO_records_${new Date().toISOString().slice(0, 10)}.xlsx`);
    };
})();
</script>
