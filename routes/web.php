<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;

Route::get('/', [TableController::class, 'index'])->name('home');
Route::get('/tables/{name}', [TableController::class, 'show'])->name('table.show');
Route::get('/places', [TableController::class, 'places'])->name('places');
Route::delete('/places/{id}', [TableController::class, 'destroy'])->name('places.destroy');
Route::get('/places/{id}', [TableController::class, 'showPlace'])->name('places.showPlace');
Route::get('/places/create', [TableController::class, 'createPlace'])->name('places.createPlace');
Route::put('/places/{id}', [TableController::class, 'updatePlace'])->name('places.update');
