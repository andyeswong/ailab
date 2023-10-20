<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIModelsController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// group of routes for v1
Route::prefix('v1')->group(function () {
    // group of routes for models
    Route::prefix('models')->group(function () {
        Route::post('/',[APIModelsController::class, 'store']);
        Route::post('/{model_token}/status',[APIModelsController::class, 'updateStatus']);
        Route::get('/{model_token}/metrics',[APIModelsController::class, 'getMetrics']);
        Route::get('/user/{user_id}',[APIModelsController::class, 'getModelsByUser']);

    });

    // group of routes for datasets
    Route::prefix('datasets')->group(function () {
        Route::post('/',[APIDataSetsController::class, 'store']);
        Route::get('/user/{user_id}',[APIDataSetsController::class, 'getDataSetsByUser']);
    });

});
