<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Symfony\Component\HttpFoundation\Request;
use App\Http\Requests\AttackCoordinatesRequest;

class GameController extends Controller
{
    public function createGame(Request $request)
    {
        $player1 = new Player([
            'user_id' => $request->user->id,
            'score' => 0
        ]);

        $game = Game::create([
            'player_1_id' => $player1->id,
            'player_2_id' => null,
            'winner' => null,
            'available_slots' => 1,
            'status' => 'setup',
            'current_player_id' => 1,
            'creator_id' => $request->user->id
        ]);

        return response()->json(['game_id' => $game->id, 'message' => 'Game created successfully!']);
    }

    public function joinGame(Request $request, $gameId)
    {
        $game = Game::find($gameId);
        if(!$game) return response()->json(['message' => 'Game not found!', 404]);
        if($game->status == 'waiting_for_players') {
            if($game->available_slots == 1) {
                $player2 = new Player([
                    'user_id' => $request->user->id,
                    'score' => 0
                ]);
                $game->player_1()->attach($player2->id);
                $game->available_slots = 0;
                $game->status = 'game_full';
                $game->save();
                return response()->json(['message' => 'Game joined successfully!']);
            } else {
                return response()->json(['message' => 'Game is full!'], 400);
            }
        } else {
            return response()->json(['message' => 'Game unavailable!'], 400);
        }
    }

    public function getCurentPlayer(Game $game)
    {
        return Player::find($game->current_player_id);
    }

    public function switchTurn(Game $game)
    {
        $game->current_player_id = ($game->current_player_id == 1) ? 2 : 1;
    }

    public function fire(AttackCoordinatesRequest $request, Game $game)
    {
        $x_coords = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10];

        $x_coordinate = $x_coords[$request->input('x_coordinate')];
        $y_coordinate = $request->input('y_coordinate');

        $currentPlayer = $this->getCurrentPlayer($game);
        $opponent = ($currentPlayer->id == 2) ? Player::find(2): Player::find(1);

        if($this->isShotAlreadyFired($x_coordinate, $y_coordinate, $opponent->grid))
        {
            return "Coordinates already attacked";
        }

        $hit = $this->checkHitOrMiss($x_coordinate, $y_coordinate, $opponent->grid);
        if($hit) {
            $opponent->grid->updateCell($x_coordinate, $y_coordinate, 'hit');
            $currentPlayer->update(['score' => $currentPlayer->score += 100]);
            $this->checkGameStatus($game);
            return response()->json(['message' => 'Hit! Keep firing!']);
        } else {
            $opponent->grid->updateCell($x_coordinate, $y_coordinate, 'miss');
            $this->switchTurn($game);
            return response()->json(['message' => 'Miss! Wait for your turn']);
        }

    }

    public function checkHitOrMiss($x_coordinate, $y_coordinate, $opponentGrid)
    {
        $cellStatus = $opponentGrid->getCellStatus($x_coordinate, $y_coordinate);

        if($cellStatus == 'ship')
        {
            return true;
        } else {
            return false;
        }
    }

    public function isShotAlreadyFired($x_coordinate, $y_coordinate, $opponentGrid)
    {
        $cellStatus = $opponentGrid->getCellStatus($x_coordinate, $y_coordinate);

        if($cellStatus != 'empty') {
            return true;
        } else {
            return false;
        }
    }

    public function checkGameStatus(Game $game)
    {
        $player1 = $game->player_1->get();
        $player2 = $game->player_2->get();
        $player1ships = $player1->deployedShips->get();
        $player2ships = $player2->deployedShips->get();
        $player1SunkenShips = $player1ships->where('is_sunk', true)->get();
        $player2SunkenShips = $player2ships->where('is_sunk', true)->get();
        $winner = null;
        if($player1ships->count() == $player1SunkenShips->count())
        {
            $game->status = 'player_1_wins';
            $game->save();
            $player1->winner = true;
            $player1->save();
            $winner = 'Player 1';
        } else if($player2ships->count() == $player2SunkenShips->count())
        {
            $game->status = 'player_2_wins';
            $game->save();
            $player2->winner = true;
            $player2->save();
            $winner = 'Player 2';
        }
        return response()->json(['message' => 'Game Over! ' . $winner . ' wins!']);
    }
}
