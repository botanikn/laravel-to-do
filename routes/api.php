<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskTagController;

Route::post('/login', [LoginController::class, 'login']);

Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(\App\Http\Middleware\CheckApiToken::class)->group(function () {
    // Эндпоинты для task
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/task/{id}', [TaskController::class, 'findById']);
    Route::post('/task', [TaskController::class, 'store']);
    Route::put('/task/{id}', [TaskController::class, 'update']);
    Route::delete('/task/{id}', [TaskController::class, 'destroy']);

    // Эндпоинты для tag
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tag/{id}', [TagController::class, 'findById']);
    Route::post('/tag', [TagController::class, 'store']);
    Route::put('/tag/{id}', [TagController::class, 'update']);
    Route::delete('/tag/{id}', [TagController::class, 'destroy']);

    // Эндпоинты для task_tag
    Route::get('/tasks_tags/{id}', [TaskTagController::class, 'findTasksByTagId']);
    Route::post('/task_tag', [TaskTagController::class, 'addTagToTask']);
    Route::delete('/task_tag', [TaskTagController::class, 'deleteTagFromTask']);
});
