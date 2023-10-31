<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ship extends Model
{
    use HasFactory;

    protected $fillable = [
        'length',
        'is_sunk',
        'points_left',
        'player_id',
        'grid_id'
    ];

    public function position(): HasOne
    {
        return $this->hasOne(Position::class);
    }

    public function grid(): BelongsTo
    {
        return $this->belongsTo(Grid::class);
    }
}
