<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grid extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'grid_state',
    ];

    protected $casts = [
        'grid_state' => 'array'
    ];

    public function getGridStateAttribute($val)
    {
        return json_decode($val, true);
    }

    public function setGridStateAttribute($val)
    {
        return $attributes['grid_state'] = json_encode($val);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ships(): HasMany
    {
        return $this->hasMany(Ship::class);
    }

    public function getCellStatus($row, $col)
    {
        $this->grid_state[$row][$col]['status'];
    }

    public function updateCell($row, $col, $status)
    {
        $this->grid_state[$row][$col]['status'] = $status;
    }
}
