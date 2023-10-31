<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
    ];

    public function grid(): HasOne
    {
        return $this->hasOne(Grid::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deployedShips(): HasMany
    {
        return $this->hasMany(Ship::class);
    }

    public function game(): HasOne
    {
        return $this->hasOne(Game::class);
    }
}
