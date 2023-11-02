<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use App\Http\Requests\AttackCoordinatesRequest;

class GameController extends Controller
{
    public function showAvailableGames()
    {
        $availableGames = Game::where('creator_id', '!=', request()->user->id)
                            ->where('available_slots', 1)
                            ->select('id', 'status', 'available_slots')
                            ->get();
        return response()->json(['games' => $availableGames], 200);
    }
    
    public function createGame(Request $request)
    {
        $player1 = $request->user;

        $player1->update([
            'score' => 0,
            'player_number' => 1
        ]);

        $game = Game::create([
            'player_1_id' => $player1->id,
            'player_2_id' => NULL,
            'winner' => NULL,
            'available_slots' => 1,
            'status' => 'waiting_for_players',
            'current_player_id' => $player1->id,
            'creator_id' => $player1->id,
        ]);

        return response()->json(['game_id' => $game->id, 'message' => 'Game created successfully!']);
    }

    public function joinGame(Request $request, $gameId)
    {
        $game = Game::find($gameId);
        if(!$game) return response()->json(['message' => 'Game not found!', 404]);
        if($game->status == 'waiting_for_players') {
            if($game->available_slots < 2) {
                $player2 = $request->user;

                $player2->update([
                    'score' => 0,
                    'player_number' => 2
                ]);
                $game->player_2()->associate($player2->id);
                $game->available_slots = 0;
                $game->status = 'game_full';
                $game->save();
                return response()->json(['message' => 'Game joined successfully!']);
            } else {
                return response()->json(['message' => 'Game is full!'], 400);
            }
        } else {
            return response()->json(['message' => 'Game unavailable!', 'availavble slots' => $game->available_slots], 400);
        }
    }

    public function getCurentPlayer(Game $game)
    {
        return User::find($game->current_player_id);
    }

    public function switchTurn(Game $game)
    {
        $game->current_player_id = ($game->current_player_id == $game->player_1->id) ? $game->player_2->id : $game->player_1->id;
    }

    public function fire(AttackCoordinatesRequest $request, Game $game)
    {
        $x_coords = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10];

        $x_coordinate = $x_coords[$request->input('x_coordinate')];
        $y_coordinate = $request->input('y_coordinate');

        $currentPlayer = $this->getCurrentPlayer($game);
        $opponent = ($currentPlayer->id == $game->player_2->id) ? $game->player_1: $game->player_2;

        if($this->isShotAlreadyFired($x_coordinate, $y_coordinate, $opponent->grid))
        {
            return "Coordinates already attacked";
        }

        $hit = $this->checkHitOrMiss($x_coordinate, $y_coordinate, $opponent->grid);
        if($hit) {
            $opponent->grid->updateCell($x_coordinate, $y_coordinate, 'hit');
            $targetedShip = $this->getTargetedShip($opponent->grid, $x_coordinate, $y_coordinate);
            if($targetedShip) {
                $this->checkIfShipIsSunk($targetedShip);
            }
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

    public function checkIfShipIsSunk($ship)
    {
        $position = $ship->position;
        $start_x = $position->from_col;
        $start_y = $position->from_row;
        $end_x = $position->to_col;
        $end_y = $position->to_row;

        $allHit = true;
        for($x = $start_x; $x<= $end_x; $x++) {
            for($y = $start_y; $y<= $end_y; $y++) {
                if($ship->grid->getCellStatus($x. $y) != 'hit') {
                    $allHit = false;
                    break 2;
                }
            }
        }
        if($allHit) $ship->update(['is_sunk' => true]);
    }

    public function getTargetedShip($grid, $x, $y) {
        $ships = $grid->ships;
        foreach($ships as $ship)
        {
            $start_x = $ship->position->from_col;
            $start_y = $ship->position->from_row;
            $end_x = $ship->position->to_col;
            $end_y = $ship->position->to_row;
            if($x >= $start_x && $x <= $end_x && $y >= $start_y && $y <= $end_y) {
                return $ship;
            }
        }
        return null;
    }
}
