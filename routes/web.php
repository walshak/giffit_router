<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\WebNetworkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Dashboard
Route::middleware('auth')->group(function () {
    // Route::get('/', [WebNetworkController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [WebNetworkController::class, 'dashboard'])->name('dashboard');

    // Router routes
    Route::get('/routers', [WebNetworkController::class, 'routers'])->name('routers');
    Route::post('/routers', [WebNetworkController::class, 'storeRouter'])->name('routers.store');
    Route::put('/routers/{id}', [WebNetworkController::class, 'updateRouterWeb'])->name('routers.update');
    Route::delete('/routers/{id}', [WebNetworkController::class, 'destroyRouter'])->name('routers.destroy');

    // Plan routes
    Route::get('/plans', [WebNetworkController::class, 'plans'])->name('plans');
    Route::post('/plans', [WebNetworkController::class, 'storePlan'])->name('plans.store');
    Route::put('/plans/{id}', [WebNetworkController::class, 'updatePlanWeb'])->name('plans.update');
    Route::delete('/plans/{id}', [WebNetworkController::class, 'destroyPlan'])->name('plans.destroy');

    Route::get('/userplans', [WebNetworkController::class, 'userPlansIndex'])->name('userplans');
    Route::post('/userplans', [WebNetworkController::class, 'userPlansStore'])->name('userplans.store');
    Route::put('/userplans/{id}', [WebNetworkController::class, 'userPlansUpdate'])->name('userplans.update');
    Route::delete('/userplans/{id}', [WebNetworkController::class, 'userPlansDestroy'])->name('userplans.destroy');


    // User routes
    Route::get('/users', [WebNetworkController::class, 'users'])->name('users');
    Route::post('/users', [WebNetworkController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [WebNetworkController::class, 'updateUserWeb'])->name('users.update');
    Route::delete('/users/{id}', [WebNetworkController::class, 'destroyUser'])->name('users.destroy');

    // Subscription route
    Route::post('/subscribe', [WebNetworkController::class, 'subscribeUser'])->name('users.subscribe');
});
