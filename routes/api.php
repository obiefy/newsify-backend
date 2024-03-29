<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/news', [NewsController::class, 'index']);
Route::get('/filters', [NewsController::class, 'filters']);
Route:: get('/feed', [NewsController::class, 'feed'])->middleware(['auth:sanctum']);
Route:: post('/follow', FollowController::class)->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
