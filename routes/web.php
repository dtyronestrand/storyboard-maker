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

Route::put('modules/{module}/items/{itemId}', [ModuleItemController::class, 'updateItem'])->middleware(['auth', 'verified'])->name('modules.items.updateItem');

Route::delete('modules/{module}/items/{itemId}', [ModuleItemController::class, 'deleteItem'])->middleware(['auth', 'verified'])->name('modules.items.deleteItem');

Route::delete('modules/{module}/items/{itemId}', [ModuleItemController::class, 'destroy'])->middleware(['auth', 'verified'])->name('modules.items.destroy');    

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
