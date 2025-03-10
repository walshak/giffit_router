<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NetworkController;
use App\Http\Controllers\Api\SystemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me'])->middleware(['auth:api']);
});

Route::group([
    'prefix' => 'system'
], function ($router) {
    Route::get('addr', [SystemController::class, 'getAddresses']);
});

Route::prefix('network')->group(function () {
    // Router routes
    Route::get('/routers', [NetworkController::class, 'getRouters']);
    Route::post('/add-router', [NetworkController::class, 'addRouter']);
    Route::put('/update-router/{id}', [NetworkController::class, 'updateRouter']);
    Route::delete('/delete-router/{id}', [NetworkController::class, 'deleteRouter']);

    // Plan routes
    Route::get('/plans', [NetworkController::class, 'getPlans']);
    Route::post('/add-plan', [NetworkController::class, 'addPlan']);
    Route::put('/update-plan/{id}', [NetworkController::class, 'updatePlan']);
    Route::get('/plans/{id}', [NetworkController::class, 'showPlan']);
    Route::delete('/delete-plan/{id}', [NetworkController::class, 'deletePlan']);

    // User routes
    Route::get('/users', [NetworkController::class, 'getUsers']);
    Route::post('/add-user', [NetworkController::class, 'addUser']);
    Route::put('/update-user/{id}', [NetworkController::class, 'updateUser']);
    Route::get('/users/email', [NetworkController::class, 'getUserByEmail']);
    Route::get('/users/{id}', [NetworkController::class, 'showUser']);
    Route::delete('/delete-user/{id}', [NetworkController::class, 'deleteUser']);

    // Subscription route
    Route::post('/subscribe-user', [NetworkController::class, 'subscribeUserToPlan']);
});
