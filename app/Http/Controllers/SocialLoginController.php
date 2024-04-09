<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function socialLogin($provider): JsonResponse
    {
        $user = Socialite::driver($provider)->stateless()->user();
        $orign = session('origin', false);

        if($orign === 'login'){
            //user is trying to login
            $internaluUser = User::where('email', $user->email)->first();


        } elseif ($orign === 'register'){

            $token = session('invitation_token');
            $inv = Invitation::where('token',$token)->first();
            if($inv){
                $inv->is_used = true;
                $inv->save();
            }

            $internaluUser = User::updateOrCreate([
                'name' => $user->name,
            ], [
                'email' => $user->email,
                'avatar' => $user->avatar,
                'password' => Hash::make(Str::random(8))
            ]);
        }

        Auth::login($internaluUser);

        return Response::json(['data' => $internaluUser]);
    }
}
