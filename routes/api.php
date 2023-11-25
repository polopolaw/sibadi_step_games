<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/registration', RegisterController::class);
    Route::post('/login', LoginController::class);
});

// Rooms
Route::group(['prefix' => 'rooms', 'middleware' => 'auth:sanctum'], function () {
    Route::post('{room}/step', [RoomController::class, 'createStep']);
    Route::resource('/', RoomController::class);
    Route::post('/enter/{room}', [RoomController::class, 'enter']);
    Route::post('/leave/{room}', [RoomController::class, 'leave']);
    Route::get('/get-updates/{room}', [RoomController::class, 'getUpdates']);
});

Route::get('/me', MeController::class)->middleware('auth:sanctum');


