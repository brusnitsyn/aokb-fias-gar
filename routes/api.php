<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/find', [\App\Http\Controllers\Api\v1\FindController::class, 'findAdm']);
    Route::prefix('{objectid}')->group(function () {
        Route::get('district', [\App\Http\Controllers\Api\v1\FindController::class, 'getDistrict']);
    });
});
