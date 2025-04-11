<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;

Route::get('/', [TableController::class, 'index'])->name('home');
Route::get('/tables/{name}', [TableController::class, 'show'])->name('table.show');
Route::resource('užsakymai', TableController::class);
Route::resource('kelionės', TableController::class);
Route::resource('lankytinos_vietos', TableController::class);
Route::resource('maršruto_taškai', TableController::class);
Route::resource('viešbučiai', TableController::class);
