<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Remark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RemarkController extends Controller
{
    public function index(Request $request): View
    {
        $remarks = Remark::query()
            ->when($request->filled('search'), fn ($query) => $query
                ->where('name', 'like', '%'.trim($request->input('search')).'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('remarks.index', ['remarks' => $remarks]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:remarks,name'],
        ]);

        $remark = Remark::create($validated);
        AuditLog::record('remark.created', "Added remark {$remark->name}");

        return back()->with('success', 'Remark added.');
    }

    public function destroy(Remark $remark): RedirectResponse
    {
        $remark->delete();
        AuditLog::record('remark.deleted', "Deleted remark {$remark->name}");

        return back()->with('success', 'Remark deleted.');
    }
}
