<?php

namespace App\Http\Controllers;

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
        $internaluUser = User::updateOrCreate([
            'name' => $user->name,
        ], [
            'email' => $user->email,
            'avatar' => $user->avatar,
            'password' => Hash::make(Str::random(8))
        ]);

        Auth::login($internaluUser);

        return Response::json(['data' => $internaluUser]);
    }
}
