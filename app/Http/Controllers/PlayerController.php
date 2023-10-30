<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Ship;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ShipSetupRequest;

class PlayerController extends Controller
{
    public function setUpShips(ShipSetupRequest $request)
    {
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
                $ship->save();

                $positions = $shipPosition['positions'];

                foreach($positions as $position) {
                    $position = new Position();
                    $position->from_row = $position['from']['row'];
                    $position->from_col = $position['from']['col'];
                    $position->to_row = $position['to']['row'];
                    $position->to_col = $position['to']['col'];

                    $ship->positions()->save($position);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }
}
