<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['dr', 'store', 'product', 'qty', 'cost', 'remarks', 'added_by'])]
class BoDraftEntry extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<BoDraft, $this>
     */
    public function draft(): BelongsTo
    {
        return $this->belongsTo(BoDraft::class, 'bo_draft_id');
    }
}
