<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->where('is_super_admin', false)
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim($request->input('search'));
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->orderByRaw("status = 'pending' desc") // approvals first
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', ['users' => $users]);
    }

    public function approve(User $user): RedirectResponse
    {
        abort_if($user->is_super_admin, 403);

        if ($user->is_admin || $user->status !== 'pending') {
            return back()->with('error', 'This account cannot be approved.');
        }

        $user->update(['status' => 'active']);
        AuditLog::record('user.approved', "Approved {$user->name} ({$user->email})");

        return back()->with('success', "{$user->name} has been approved and can now sign in.");
    }

    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_super_admin, 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        if (! $user->is_admin && $user->status !== 'active') {
            return back()->with('error', 'Approve this account before making it an admin.');
        }

        $user->update(['is_admin' => ! $user->is_admin]);
        AuditLog::record('user.role', $user->is_admin
            ? "Made {$user->name} an admin"
            : "Removed admin from {$user->name}");

        return back()->with('success', $user->is_admin
            ? "{$user->name} is now an admin."
            : "{$user->name} is now a regular user.");
    }

    public function toggle(User $user): RedirectResponse
    {
        abort_if($user->is_super_admin, 403);

        if ($user->is_admin) {
            return back()->with('error', 'Admin accounts cannot be disabled.');
        }

        if ($user->status === 'pending') {
            return back()->with('error', 'Approve this account first.');
        }

        $user->update(['status' => $user->status === 'active' ? 'disabled' : 'active']);
        AuditLog::record(
            $user->status === 'active' ? 'user.enabled' : 'user.disabled',
            ($user->status === 'active' ? 'Enabled ' : 'Disabled ')."{$user->name} ({$user->email})",
        );

        return back()->with('success', $user->status === 'active'
            ? "{$user->name} has been enabled."
            : "{$user->name} has been disabled.");
    }
}
