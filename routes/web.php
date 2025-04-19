<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;

Route::get('/', [TableController::class, 'index'])->name('home');
Route::get('/tables/{name}', [TableController::class, 'show'])->name('table.show');
Route::get('/places', [TableController::class, 'places'])->name('places');
Route::delete('/places/{id}', [TableController::class, 'destroy'])->name('places.destroy');
Route::get('/places/{id}', [TableController::class, 'showPlace'])->name('places.showPlace');
Route::get('/places/create', [TableController::class, 'createPlace'])->name('places.createPlace');
Route::get('/places/{id}/edit', [TableController::class, 'edit'])->name('places.edit');
// Route to show the edit form for a maršrutai record
Route::get('/routes/{id}/edit', [TableController::class, 'edit'])->name('places.edit');

// Route to handle the update request for a maršrutai record
Route::put('/routes/{id}/edit', [TableController::class, 'update'])->name('places.updatePlace');
Route::put('places/{id}/edit', [TableController::class, 'update'])->name('places.update');
