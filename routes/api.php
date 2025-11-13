<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\CheckApiToken;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes (require API token)
Route::middleware(CheckApiToken::class)->group(function () {
    // Task routes
    Route::apiResource('tasks', TaskController::class);
    Route::post('attach-tag', [TaskController::class, "attach"]);
    Route::delete('detach-tag', [TaskController::class, "detach"]);

    // Tag routes
    Route::apiResource('tags', TagController::class);
});
