<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim($request->input('search'));
                $query->where(fn ($query) => $query
                    ->where('user_name', 'like', "%{$term}%")
                    ->orWhere('action', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%"));
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('audit.index', [
            'logs' => $logs,
            'pruneCutoff' => now()->subMonths(2),
            'prunableCount' => AuditLog::where('created_at', '<', now()->subMonths(2))->count(),
        ]);
    }

    /**
     * JSON rows for the client-side Excel export (optional date range,
     * inclusive on both ends).
     */
    public function export(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = AuditLog::query()
            ->when($data['from'] ?? null, fn ($query, $from) => $query->where('created_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($data['to'] ?? null, fn ($query, $to) => $query->where('created_at', '<=', Carbon::parse($to)->endOfDay()))
            ->orderBy('id')
            ->get();

        return response()->json($logs->map(fn ($log) => [
            'when' => $log->created_at->format('M d, Y g:i A'),
            'user' => $log->user_name ?? 'System',
            'action' => $log->action,
            'description' => $log->description,
        ])->values());
    }

    /**
     * Delete entries older than two months (the recent trail is untouchable).
     * Requires the admin to re-confirm their password.
     */
    public function prune(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $cutoff = now()->subMonths(2);
        $count = AuditLog::where('created_at', '<', $cutoff)->delete();

        if ($count) {
            AuditLog::record('audit.pruned', "Deleted {$count} log entries older than {$cutoff->format('M d, Y')}");
        }

        return back()->with('success', $count
            ? "{$count} old log entries deleted."
            : 'No log entries older than 2 months.');
    }
}
