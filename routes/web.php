<?php

use App\Http\Controllers\LotController;
use App\Http\Controllers\StaticLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/lotti');

Route::middleware('guest')->group(function () {
    Route::get('/login', [StaticLoginController::class, 'create'])->name('login');
    Route::post('/login', [StaticLoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [StaticLoginController::class, 'destroy'])
    ->middleware('static.auth')
    ->name('logout');

Route::middleware('static.auth')->group(function () {
    Route::get('/lotti', [LotController::class, 'edit'])->name('lots.edit');
    Route::post('/lotti', [LotController::class, 'update'])->name('lots.update');
});
