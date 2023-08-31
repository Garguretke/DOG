<?php

//use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\LiqCalcController;
//use App\Http\Controllers\PlayerController;
//use App\Http\Controllers\QRCodeController;
//use App\Http\Controllers\SeweyController;
//use App\Http\Controllers\eMealController;

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

Route::group(['middleware'=>['auth']], function()
{
    // Kalkulator
    Route::get('/liqcalc', 'LiqCalcController@getIndex')->name('liqcalc.getIndex');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //Player
    Route::get("/player/{series}", 'PlayerController@index')->name('player');
    Route::get('/player/naruto', 'PlayerController@naruto')->name('naruto');
    Route::get('/player/shippuuden', 'PlayerController@shippuuden')->name('shippuuden');
    Route::get('/player/boruto', 'PlayerController@boruto')->name('boruto');

    // Generator QR - formularz
    Route::get('/generator-qr', 'QRCodeController@index')->name('qrcode.generator');

    // Generator QR - przetwarzanie danych
    Route::post('/generator-qr', 'QRCodeController@generate')->name('qrcode.generate');

    // Sewey
    Route::get('/sewey', 'SeweyController@sewey')->name('sewey');

    //eMeal
    Route::get('/emeal', 'eMealController@emeal_index')->name('emeal_index');
    Route::get('/emeal/products', 'eMealController@emeal_products')->name('emeal_products');
    Route::get('/emeal/products/loadInfo', 'eMealController@emeal_products_store')->name('emeal_products_store');

    Route::get('/emeal/recipes', 'eMealController@emeal_recipes')->name('emeal_recipes');
});

//



//// Kalkulator

