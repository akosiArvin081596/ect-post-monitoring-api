<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\SurveyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Auth
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Incidents
        Route::get('/incidents', [IncidentController::class, 'index']);

        // Surveys
        Route::apiResource('surveys', SurveyController::class);
        Route::post('/surveys/{survey}/uploads', [SurveyController::class, 'upload']);
    });

    // Public address endpoints
    Route::get('/addresses/provinces', [AddressController::class, 'provinces']);
    Route::get('/addresses/districts', [AddressController::class, 'districts']);
    Route::get('/addresses/municipalities', [AddressController::class, 'municipalities']);
});
