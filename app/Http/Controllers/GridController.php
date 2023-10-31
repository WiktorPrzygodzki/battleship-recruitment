<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipSetupRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Ship;
use App\Models\Position;
use Exception;
use App\Models\Grid;

class GridController extends Controller
{
    public function initializeGrid()
    {
        $grid = null;
        for($row=0; $row<10; $row++){
            for($col=0; $col<10; $col++){
                $grid[$row][$col] = [
                    'status' => 'empty',
                    'ship_id' => null
                ];
            }
        }
        return $grid;
    }

    public function setUpShips(ShipSetupRequest $request)
    {
        $playerId = auth()->user()->id;
        $grid = new Grid();
        $grid->player_id = $playerId;
        $grid->grid_state = $this->initializeGrid();
        $grid->save();

        $shipPositions = $request->input('ship_positions');
        foreach($shipPositions as $shipPosition) {
            $shipLength = $shipPosition['length'];

            if(!in_array($shipLength, [4, 3, 2])) {
                return 'Invalid ship length';
            }

            DB::beginTransaction();
            try {
                $ship = new Ship();
                $ship->length = $shipLength;
                $ship->grid_id = $grid->id;
                $ship->save();

                $positions = $shipPosition['positions'];

                foreach($positions as $position) {
                    $position = new Position();
                    $position->from_row = $position['from']['row'];
                    $position->from_col = $position['from']['col'];
                    $position->to_row = $position['to']['row'];
                    $position->to_col = $position['to']['col'];

                    $ship->position()->save($position);

                    $this->updateGridState($position, $grid);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

    public function updateGridState(Position $position, Grid $grid)
    {
        $gridState = $grid->grid_state;
        for($row = $position->from_row; $row <= $position->to_row; $row++) {
            for($col = $position->from_col; $col <= $position->to_col; $col++) {
                $gridState[$row][$col]['status'] = 'ship';
            }
        }
        $grid->save();

    }
}
