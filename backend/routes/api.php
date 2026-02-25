<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\VehicleManager\LookupsController;
use App\Http\Controllers\Api\VehicleManager\VehiclesController;
use App\Http\Controllers\Api\VehicleManager\AssignmentsController;
use App\Http\Controllers\Api\VehicleManager\DocumentsController;

Route::prefix('vehicle-manager')->group(function () {
    Route::get('lookups', [LookupsController::class, 'index']);

    Route::get('vehicles', [VehiclesController::class, 'index']);
    Route::get('vehicles/{id}', [VehiclesController::class, 'show']);
    Route::post('vehicles', [VehiclesController::class, 'store']);
    Route::put('vehicles/{id}', [VehiclesController::class, 'update']);
    Route::delete('vehicles/{id}', [VehiclesController::class, 'destroy']);

    Route::get('vehicles/{id}/assignments', [AssignmentsController::class, 'index']);
    Route::get('vehicles/{id}/documents', [DocumentsController::class, 'index']);
});
