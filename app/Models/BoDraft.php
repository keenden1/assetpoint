<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'date', 'dr', 'store', 'bo_id'])]
class BoDraft extends Model
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
        ];
    }

    /**
     * The single shared draft everyone edits together.
     */
    public static function current(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'type' => 'B.O',
            'date' => now()->toDateString(),
        ]);
    }

    /**
     * @return HasMany<BoDraftEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(BoDraftEntry::class);
    }
}
