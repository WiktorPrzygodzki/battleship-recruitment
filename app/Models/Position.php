<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'ship_id',
        'from_row',
        'to_row',
        'from_col',
        'to_col'
    ];

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }
}
