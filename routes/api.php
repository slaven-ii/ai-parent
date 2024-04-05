<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/sanctum/token', TokenController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/auth', AuthController::class);
    Route::post('/ask', [ThreadsController::class, 'ask']);
    Route::post('/check', [ThreadsController::class, 'checkThreadStatus']);
    Route::get('/threads', [ThreadsController::class, 'list']);
    Route::post('/threads/messages', [ThreadsController::class, 'messagesList']);
});



Route::middleware(['web'])->group(function () {
//Routes for social login/registration
    Route::get('/auth/redirect/{provider}', function (Request $request, $provider) {
        return Response::json(['data' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()]);
    });
    Route::get('/auth/callback/{provider}', [SocialLoginController::class, 'socialLogin']);
});

