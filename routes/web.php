<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;

Route::get('/', [TableController::class, 'index'])->name('home');
Route::get('/tables/{name}', [TableController::class, 'show'])->name('table.show');
Route::get('/places', [TableController::class, 'places'])->name('places');
