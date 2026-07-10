<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'type', 'date', 'archived_at'])]
class Bo extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<BoItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(BoItem::class);
    }

    /**
     * @return HasMany<BoActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(BoActivity::class);
    }

    /**
     * Record an audit-trail entry, snapshotting the actor's name as it was
     * at the time of the action.
     */
    public function logActivity(User $user, string $action): void
    {
        $this->activities()->create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => $action,
        ]);

        AuditLog::record(
            'bo.'.$action,
            ucfirst($action)." {$this->type} sheet dated {$this->date->format('M d, Y')}",
            $user,
        );
    }
}
