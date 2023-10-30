<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grid_id',
        'score',
    ];

    public function grid(): HasOne
    {
        return $this->hasOne(Grid::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function deployedShips(): HasMany
    {
        return $this->hasMany(Ship::class);
    }
}
