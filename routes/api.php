<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'eventos',
    'middleware' => 'api',
], function ($router) {
    Route::get('/getall', [EventController::class, 'index']);
    Route::get('/getdata/{id_event}', [EventController::class, 'show']);
    Route::post('/savedata', [EventController::class, 'store']);
    // Route::put('/updatedata/{id_event}', [EventController::class, 'update']);
    // Route::delete('/deletedata/{id_event}', [EventController::class, 'destroy']);
    // Route::put('/updatestate/{id_event}', [EventController::class, 'updatestate']);
});
