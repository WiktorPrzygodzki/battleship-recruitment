<?php

namespace App\Services;

use App\Models\Grid;
use App\Models\Ship;

class ShipService {

    protected $ships;
    protected $grid;

    function __construct($ships, Grid $grid)
    {
        $this->ships = $ships;
        $this->grid = $grid;
    }
    
    public static function createShip($length)
    {
        return Ship::create([
            'length' => $length,
            'is_sunk' => false,
            'position_id' => null,
            'points_left' => $length
        ]); 
    }

    public static function setShipsPosition(array $attributes)
    {
        
    }
}