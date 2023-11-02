<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Grid;
use App\Models\Ship;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ShipSetupRequest;

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
        Log::debug('xD');
        $playerId = $request->user->id;
        $grid = new Grid();
        $grid->player_id = $playerId;
        $grid->grid_state = $this->initializeGrid();
        $grid->save();

        $shipPositions = $request->input('ship_positions');

        $shipCountsLimits = [
            3 => 1,
            2 => 2,
            1 => 4
        ];
        
        $shipCounts = [
            3 => 0,
            2 => 0,
            1 => 0
        ];

        foreach($shipPositions as $shipPosition) {
            $shipLength = $shipPosition['length'];

            if(!in_array($shipLength, [4, 3, 2])) {
                return 'Invalid ship length';
            }

            if($shipCounts[$shipLength] >= $shipCountsLimits[$shipLength]) {
                return 'Too many ships of length ' . $shipLength;
            }

            $positions = $shipPosition['positions'];
            foreach ($positions as $position) {
                $fromRow = $position['from_row'];
                $fromCol = $position['from_col'];
                $toRow = $position['to_row'];
                $toCol = $position['to_col'];

                for ($row = $fromRow; $row <= $toRow; $row++) {
                    for ($col = $fromCol; $col <= $toCol; $col++) {
                        if ($grid->grid_state[$row][$col]['status'] === 'ship') {
                            return 'Ships overlap';
                        }
                    }
                }
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
                    $position->from_row = $position['from_row'];
                    $position->from_col = $position['from_col'];
                    $position->to_row = $position['to_row'];
                    $position->to_col = $position['to_col'];

                    $ship->position()->save($position);

                    $this->updateGridState($position, $grid);
                }

                $shipCounts[$shipLength]++;

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
