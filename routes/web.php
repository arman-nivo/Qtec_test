<?php

use App\Livewire\TaskForm;
use App\Livewire\TaskList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('tasks.index');
    }

    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Directly set tasks.index as dashboard - NO REDIRECT
    Route::get('/dashboard', TaskList::class)->name('dashboard');

    Route::get('/tasks', TaskList::class)->name('tasks.index');
    Route::get('/tasks/create', TaskForm::class)->name('tasks.create');
    Route::get('/tasks/{taskId}/edit', TaskForm::class)->name('tasks.edit');
});
