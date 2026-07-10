<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $stores = Store::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim($request->input('search'));
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%"));
            })
            ->orderByRaw('code IS NULL, code')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('stores.index', ['stores' => $stores]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:255', 'unique:stores,code'],
            'name' => ['required', 'string', 'max:255', 'unique:stores,name'],
        ]);

        $store = Store::create($validated);
        AuditLog::record('store.created', 'Added store '.trim("{$store->name} ({$store->code})", ' ()'));

        return back()->with('success', 'Store added.');
    }

    public function destroy(Store $store): RedirectResponse
    {
        $store->delete();
        AuditLog::record('store.deleted', 'Deleted store '.trim("{$store->name} ({$store->code})", ' ()'));

        return back()->with('success', 'Store deleted.');
    }
}
