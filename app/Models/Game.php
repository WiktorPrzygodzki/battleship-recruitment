<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_1_id',
        'player_2_id',
        'winner',
        'available_slots',
        'status',
        'current_player_id',
        'creator_id'
    ];

    public function player_1(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_1_id', 'id');
    }

    public function player_2(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_2_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
