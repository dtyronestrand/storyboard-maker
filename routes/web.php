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

Route::get('/google/redirect', [\App\Http\Controllers\StoryboardController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [\App\Http\Controllers\StoryboardController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/storyboard/export/{course}', [\App\Http\Controllers\StoryboardController::class, 'exportToGoogleDocs'])->name('storyboard.export');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
