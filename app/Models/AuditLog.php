<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

#[Fillable(['user_id', 'user_name', 'action', 'description'])]
class AuditLog extends Model
{
    /**
     * Write an audit entry for the given (or current) user.
     */
    public static function record(string $action, string $description, ?User $user = null): void
    {
        $user ??= Auth::user();

        static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'description' => $description,
        ]);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
