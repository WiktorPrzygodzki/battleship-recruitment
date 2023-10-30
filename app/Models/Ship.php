<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ship extends Model
{
    use HasFactory;

    protected $fillable = [
        'length',
        'is_sunk',
        'points_left',
    ];

    public function positions(): HasMany
    {
        return $this->hasmany(Position::class);
    }
}
