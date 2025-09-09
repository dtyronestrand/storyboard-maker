<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleItemController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [CourseController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('courses', CourseController::class)->middleware(['auth', 'verified']);

Route::resource('modules', ModuleController::class)->middleware(['auth', 'verified']);

Route::put('modules/{module}/items', [ModuleItemController::class, 'update'])->middleware(['auth', 'verified'])->name('modules.items.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
