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
    Route::resource('tasks', TaskController::class)
    ->except(['show', 'create', 'edit']);

    Route::get('tasks/{id}', [TaskController::class, 'findById'])->name('tasks.show');

    // Эндпоинты для tag
    Route::resource('tags', TagController::class)
    ->except(['show', 'create', 'edit']);

    Route::get('tags/{id}', [TagController::class, 'findById'])->name('tags.show');

    // Route::get('/tags', [TagController::class, 'index']);
    // Route::get('/tag/{id}', [TagController::class, 'findById']);
    // Route::post('/tag', [TagController::class, 'store']);
    // Route::put('/tag/{id}', [TagController::class, 'update']);
    // Route::delete('/tag/{id}', [TagController::class, 'destroy']);

    // Эндпоинты для task_tag
    Route::get('/task_tag/{id}', [TaskTagController::class, 'findTasksByTagId']);
    Route::post('/task_tag', [TaskTagController::class, 'addTagToTask']);
    Route::delete('/task_tag', [TaskTagController::class, 'deleteTagFromTask']);
});
