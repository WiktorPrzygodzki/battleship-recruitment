<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GameController extends Controller
{
    public function gameSetup()
    {
        $game = Game::create([
            'player_1_id' => null,
            'player_2_id' => null,
            'winner' => null,
            'available_slots' => 2,
            'status' => 'setup'
        ]);
    }
}
