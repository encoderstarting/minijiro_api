<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;
Route::post('login',[authController::class,'login']);
Route::post('refresh',[authController::class,'refresh']);
Route::middleware('auth:api')->group(function(){
    Route::post('logout',[authController::class,'logout']);
    Route::get('me',[authController::class,'me']);
    Route::apiResource('projects', ProjectController::class);

});


