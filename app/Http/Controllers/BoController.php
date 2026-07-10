<?php

namespace App\Http\Controllers;

use App\Models\Bo;
use App\Models\Product;
use App\Models\Remark;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->get()
            ->sortBy([
                fn ($a, $b) => strcmp($a->category->name, $b->category->name),
                fn ($a, $b) => $a->id <=> $b->id,
            ])
            ->values();

        return view('bo.index', [
            'products' => $products,
            'stores' => Store::orderBy('name')->get(),
            'remarks' => Remark::orderBy('name')->get(),
        ]);
    }

    public function history(Request $request): View
    {
        $showArchived = $request->boolean('archived');

        $bos = Bo::with('user')
            ->withCount('items')
            ->withSum('items as total', 'total')
            ->when($showArchived,
                fn ($query) => $query->whereNotNull('archived_at'),
                fn ($query) => $query->whereNull('archived_at'))
            ->latest('date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('bo.history', [
            'bos' => $bos,
            'showArchived' => $showArchived,
            'activeCount' => Bo::whereNull('archived_at')->count(),
            'archivedCount' => Bo::whereNotNull('archived_at')->count(),
        ]);
    }

    /**
     * JSON payload for the multi-record Excel export (one sheet per record).
     */
    public function export(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ])['ids'];

        $bos = Bo::with(['items', 'user'])->whereIn('id', $ids)->orderBy('date')->orderBy('id')->get();

        return response()->json($bos->map(fn ($bo) => [
            'type' => $bo->type,
            'date' => $bo->date->format('Y-m-d'),
            'savedBy' => $bo->user->name ?? '',
            'entries' => $bo->items->map(fn ($item) => [
                'dr' => $item->dr,
                'store' => $item->store,
                'product' => $item->product,
                'qty' => (int) $item->qty,
                'cost' => (float) $item->cost,
                'remarks' => $item->remarks,
            ])->values(),
        ])->values());
    }

    public function show(Bo $bo): View
    {
        $bo->load(['items', 'user', 'activities.user']);

        return view('bo.show', [
            'bo' => $bo,
            // Plain array for the client-side exporter / load-into-editor.
            'entries' => $bo->items->map(fn ($item) => [
                'dr' => $item->dr,
                'store' => $item->store,
                'product' => $item->product,
                'qty' => (int) $item->qty,
                'cost' => (float) $item->cost,
                'remarks' => $item->remarks,
            ])->values(),
        ]);
    }

    public function archive(Request $request, Bo $bo): RedirectResponse
    {
        if (! $request->user()->is_admin && $bo->user_id !== $request->user()->id) {
            abort(403);
        }

        $bo->update(['archived_at' => now()]);
        $bo->logActivity($request->user(), 'archived');

        return back()->with('success', 'BO sheet archived. You can restore it from the Archived tab.');
    }

    public function unarchive(Request $request, Bo $bo): RedirectResponse
    {
        if (! $request->user()->is_admin && $bo->user_id !== $request->user()->id) {
            abort(403);
        }

        $bo->update(['archived_at' => null]);
        $bo->logActivity($request->user(), 'restored');

        return back()->with('success', 'BO sheet restored.');
    }
}
