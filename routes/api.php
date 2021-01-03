<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JwtAuthController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CommentsController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
        ], function ($router) {
    Route::post('/register', [JwtAuthController::class, 'register']);
    Route::post('/login', [JwtAuthController::class, 'login']);
    Route::get('/get-user', [JwtAuthController::class, 'searchByUser']);
    Route::post('/token-refresh', [JwtAuthController::class, 'refresh']);
    Route::post('/delete-user', [JwtAuthController::class, 'delete']);

    Route::post('/logout', [JwtAuthController::class, 'signout']);
});

Route::apiResource('items', ItemsController::class);



Route::group([
    'middleware' => 'api',
        ], function ($router) {
    Route::get('/comments', [CommentsController::class, 'fetchComments']);

    Route::post('/comments', [CommentsController::class, 'addComments']);
});
