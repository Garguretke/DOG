<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LiqCalcController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\SeweyController;

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

Route::get('/', function () {
    return view('layouts.app');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get("/player/{series}", "PlayerController@index")->name('player');
Route::get('/player/naruto', [PlayerController::class, 'naruto'])->name('naruto');
Route::get('/player/shippuuden', [PlayerController::class, 'shippuuden'])->name('shippuuden');
Route::get('/player/boruto', [PlayerController::class, 'boruto'])->name('boruto');

// Kalkulator
Route::get('/liqcalc', [LiqCalcController::class, 'liqcalc'])->name('liqcalc');

// Generator QR - formularz
Route::get('/generator-qr', [QRCodeController::class, 'index'])->name('qrcode.generator');

// Generator QR - przetwarzanie danych
Route::post('/generator-qr', [QRCodeController::class, 'generate'])->name('qrcode.generate');

// Sewey
Route::get('/sewey', [SeweyController::class, 'sewey'])->name('sewey');

//eMeal
//Route::get('/emeal', [eMealController::class, 'emeal'])->name('emeal');