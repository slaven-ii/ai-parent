<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use Closure;
//use App\Models\Invitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckInvitation
{
    public function handle(Request $request, Closure $next)
    {
        $route = Route::getRoutes()->match($request);
        $currentroute = $route->getActionName();
        $origin = session('origin', false);

        if(($currentroute == 'Laravel\Fortify\Http\Controllers\RegisteredUserController@store'
        || $currentroute == 'App\Http\Controllers\SocialLoginController@socialLogin') && $origin === 'register'){
            $token = $request->input('invitation_token', false);

            if(!$token){
                $token = $request->session()->get('invitation_token');
            }

            if(!$token){
                return response(['error' => 'invalid invitation'], 400);
            }

            $invitation = Invitation::where('token', $token)->where('is_used', false)->first();

            if(!$invitation){
                return response(['error' => 'invalid invitation'], 400);
            }
            $request->merge(['invitation_token' => $token]);

        }

        return $next($request);
    }
}
