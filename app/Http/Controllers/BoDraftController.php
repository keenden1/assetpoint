<?php

namespace App\Http\Controllers;

use App\Models\Bo;
use App\Models\BoDraft;
use App\Models\BoDraftEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoDraftController extends Controller
{
    private const EDITORS_CACHE_KEY = 'bo-draft-editors';

    private const EDITOR_TIMEOUT_SECONDS = 15;

    /**
     * Current draft state (also acts as the polling heartbeat: pass
     * ?presence=1 to be listed as an active editor).
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json($this->payload($request));
    }

    public function updateMeta(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['sometimes', Rule::in(['B.O', 'PULL-OUT', 'RETURN'])],
            'date' => ['sometimes', 'date'],
            'dr' => ['sometimes', 'nullable', 'string', 'max:255'],
            'store' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        BoDraft::current()->update($data);

        return response()->json($this->payload($request));
    }

    public function addEntry(Request $request): JsonResponse
    {
        $data = $this->validatedEntry($request);

        // Same product for the same DR#/store: bump the quantity on the
        // existing row instead of adding a duplicate line.
        $existing = BoDraft::current()->entries()
            ->where('product', $data['product'])
            ->where('dr', $data['dr'])
            ->where('store', $data['store'])
            ->first();

        if ($existing) {
            $existing->update([
                'qty' => $existing->qty + $data['qty'],
                'cost' => $data['cost'],
                'remarks' => $data['remarks'],
            ]);
        } else {
            $data['added_by'] = $request->user()->name;
            BoDraft::current()->entries()->create($data);
        }

        return response()->json(array_merge($this->payload($request), ['merged' => (bool) $existing]));
    }

    public function updateEntry(Request $request, BoDraftEntry $entry): JsonResponse
    {
        $entry->update($this->validatedEntry($request));

        return response()->json($this->payload($request));
    }

    public function removeEntry(Request $request, BoDraftEntry $entry): JsonResponse
    {
        $entry->delete();

        return response()->json($this->payload($request));
    }

    public function clearEntries(Request $request): JsonResponse
    {
        $draft = BoDraft::current();
        $draft->entries()->delete();
        $draft->update(['bo_id' => null]);

        return response()->json($this->payload($request));
    }

    /**
     * Copy a saved record into the shared draft for editing.
     */
    public function loadRecord(Request $request, Bo $bo): JsonResponse
    {
        $draft = BoDraft::current();
        $draft->entries()->delete();
        $draft->update([
            'type' => $bo->type,
            'date' => $bo->date->format('Y-m-d'),
            'dr' => null,
            'store' => null,
            'bo_id' => $bo->id,
        ]);

        $draft->entries()->createMany($bo->items->map(fn ($item) => [
            'dr' => $item->dr,
            'store' => $item->store,
            'product' => $item->product,
            'qty' => $item->qty,
            'cost' => $item->cost,
            'remarks' => $item->remarks,
            'added_by' => $request->user()->name,
        ])->all());

        return response()->json(['redirect' => route('bo.index')]);
    }

    /**
     * Persist the shared draft as a record (new, or updating the record it
     * was loaded from), then reset the draft.
     */
    public function saveRecord(Request $request): JsonResponse
    {
        $mode = $request->validate([
            'mode' => ['required', Rule::in(['new', 'update'])],
        ])['mode'];

        $user = $request->user();

        return DB::transaction(function () use ($mode, $user) {
            // Row lock: if two users hit Save simultaneously, the second waits
            // here and then finds an empty draft instead of double-saving.
            $draft = BoDraft::query()->lockForUpdate()->findOrFail(BoDraft::current()->id);
            $draft->load('entries');

            abort_if($draft->entries->isEmpty(), 422, 'Nothing to save — the sheet may have just been saved by someone else.');

        if ($mode === 'update') {
            abort_unless($draft->bo_id, 422, 'This draft is not linked to a record.');
            $bo = Bo::findOrFail($draft->bo_id);

            if (! $user->is_admin && $bo->user_id !== $user->id) {
                abort(403);
            }

            $bo->update(['type' => $draft->type, 'date' => $draft->date]);
            $bo->items()->delete();
            $action = 'updated';
            $message = 'BO sheet updated.';
        } else {
            $bo = Bo::create([
                'user_id' => $user->id,
                'type' => $draft->type,
                'date' => $draft->date,
            ]);
            $action = 'created';
            $message = 'BO sheet saved to records.';
        }

        $bo->items()->createMany($draft->entries->map(fn ($entry) => [
            'dr' => $entry->dr ?? '',
            'store' => $entry->store ?? '',
            'product' => $entry->product,
            'qty' => $entry->qty,
            'cost' => $entry->cost,
            'total' => $entry->qty * $entry->cost,
            'remarks' => $entry->remarks ?? '',
        ])->all());

            $bo->logActivity($user, $action);

            // Fresh shared draft for the next sheet.
            $draft->entries()->delete();
            $draft->update(['bo_id' => null, 'dr' => null, 'store' => null, 'date' => now()->toDateString()]);

            session()->flash('success', $message);

            return response()->json(['redirect' => route('bo.show', $bo)]);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedEntry(Request $request): array
    {
        return $request->validate([
            'dr' => ['required', 'string', 'max:255'],
            'store' => ['required', 'string', 'max:255'],
            'product' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'cost' => ['required', 'numeric', 'min:0'],
            'remarks' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        $draft = BoDraft::current()->load('entries');

        return [
            'meta' => [
                'type' => $draft->type,
                'date' => $draft->date?->format('Y-m-d'),
                'dr' => $draft->dr ?? '',
                'store' => $draft->store ?? '',
                'boId' => $draft->bo_id,
            ],
            'entries' => $draft->entries->map(fn ($entry) => [
                'id' => $entry->id,
                'dr' => $entry->dr ?? '',
                'store' => $entry->store ?? '',
                'product' => $entry->product,
                'qty' => (int) $entry->qty,
                'cost' => (float) $entry->cost,
                'remarks' => $entry->remarks ?? '',
                'added_by' => $entry->added_by ?? '',
            ])->values(),
            'editors' => $this->editors($request),
        ];
    }

    /**
     * Presence: who else is on the B.O editor right now (last 15s).
     *
     * @return list<string>
     */
    private function editors(Request $request): array
    {
        $user = $request->user();
        $editors = Cache::get(self::EDITORS_CACHE_KEY, []);
        $cutoff = now()->timestamp - self::EDITOR_TIMEOUT_SECONDS;

        $editors = array_filter($editors, fn ($editor) => ($editor['at'] ?? 0) >= $cutoff);

        // Only the editor page (polling with presence=1) counts as "editing".
        if ($request->boolean('presence')) {
            $editors[$user->id] = ['name' => $user->name, 'at' => now()->timestamp];
        }

        Cache::put(self::EDITORS_CACHE_KEY, $editors, 60);

        return collect($editors)->except($user->id)->pluck('name')->unique()->values()->all();
    }
}
