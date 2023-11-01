<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('tokencheck')->get('/user', function (Request $request) {
    return json_encode($request->user);
});

Route::middleware('tokencheck')->group(function() {
    Route::prefix('game')->group(function() {
        Route::get('/games', [GameController::class, 'showAvailableGames'])->name('game.show');
        Route::post('/create', [GameController::class, 'createGame'])->name('game.create');
        Route::post('/join/{gameId}', [GameController::class, 'joinGame'])->name('game.join');
        Route::post('/fire', [GameController::class, 'fire'])->middleware('checkturn')->name('game.fire');
    });
});
