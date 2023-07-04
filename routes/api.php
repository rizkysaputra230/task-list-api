<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Dashboard\WorshipScheduleController;
use App\Http\Controllers\Api\Dashboard\TaskController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware(['middleware' => 'auth:sanctum'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::put('/task/{id}/update-status', [TaskController::class, 'updateStatus']);
        Route::get('chart', [TaskController::class, 'chart']);
        Route::resource('/task', TaskController::class);
    });
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::get('worship', [WorshipScheduleController::class, 'index']);
