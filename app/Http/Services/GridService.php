<?php

namespace App\Services;

use SplFixedArray;
use App\Models\Grid;

class GridService {

    public static function gridSetup()
    {
        $board = new SplFixedArray(10);
        foreach($board as $cell)
        {
            $cell = new SplFixedArray(10);
        }

        $ships = array();
        array_push($ships, ShipService::createShip(3));
        array_push($ships, ShipService::createShip(2));
        array_push($ships, ShipService::createShip(2));
        array_push($ships, ShipService::createShip(1));
        array_push($ships, ShipService::createShip(1));
        array_push($ships, ShipService::createShip(1));
        array_push($ships, ShipService::createShip(1));


        $grid = Grid::create([
            'board' => $board,
            'available_ships' => $ships
        ]);
        return $grid;
    }
}