<?php

use App\Http\Controllers\ThreadsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response("42");
    //return view('welcome');
});
/*
Route::group(['prefix' => 'threads', 'middleware' => ['auth']], function () {
    Route::get('/list', [ThreadsController::class, 'index']);
    Route::get('/create', [ThreadsController::class, 'create']);
    Route::get('/list', [ThreadsController::class, 'index']);
    Route::get('/show', [ThreadsController::class, 'show']);
});
*/
