<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlayersTurn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rightToPlay = $this->checkTurn($request->user);
        if(!$rightToPlay) {
            return response()->json(['error' => 'Please wait for your turn'], 403);
        }
        
        return $next($request);
    }

    public function checkTurn($user)
    {
        if($user->player) {
            if($user->player->game->current_player_id == $user->player->player_number) {
                return true;
            } else {
                return false;
            }
        } else {
            return response()->json(['error' => "You're not currently taking part in any game"], 403);
        }
    }
}
