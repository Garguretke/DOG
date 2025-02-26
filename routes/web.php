<?php

//use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\LiqCalcController;
//use App\Http\Controllers\PlayerController;
//use App\Http\Controllers\QRCodeController;
//use App\Http\Controllers\SeweyController;
//use App\Http\Controllers\eMealController;
use App\Services\IniFile;

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

$path = realpath(__DIR__.'/..');

if(file_exists("$path/.env")){
	$env = new IniFile("$path/.env");
} else {
	$env = new IniFile();
}

$url = str_replace(['https://','http://'], '', $env->get('APP_URL',''));
$pos = strpos($url,'/');
if($pos !== false) $url = substr($url,$pos);
if($url != '/' && $pos !== false){
	Route::get($url, function () {
		return view('layouts.app');
	});
} else {
	Route::get('/', function () {
		return view('layouts.app');
	});	
}

Route::get('/', function () {
    return view('layouts.app');
});

Auth::routes();

Route::group(['middleware'=>['auth']], function()
{
    // Liquid Calculator
    Route::get('/liqcalc', 'LiqCalcController@getIndex')->name('liqcalc.get-index');

    Route::get('/home', 'HomeController@getIndex')->name('home');

    //Player
    Route::get("/player/{series}", 'PlayerController@getIndex')->name('player.get-index');
    Route::get('/player/naruto', 'PlayerController@Naruto')->name('player.naruto');
    Route::get('/player/shippuuden', 'PlayerController@Shippuuden')->name('player.shippuuden');
    Route::get('/player/boruto', 'PlayerController@Boruto')->name('player.boruto');

    // Generator QR - form
    Route::get('/generator-qr', 'QRCodeController@getIndex')->name('qrcode.get-index');

    // Generator QR - generate
    Route::post('/generator-qr', 'QRCodeController@Generate')->name('qrcode.generate');

    // Sewey
    Route::get('/sewey', 'SeweyController@getIndex')->name('sewey.get-index');

    //eMeal
    Route::get('/emeal', 'eMealController@getIndex')->name('emeal.get-index');
    Route::get('/emeal/products', 'eMealController@eMealProducts')->name('emeal.products');
    Route::post('/emeal/products/add', 'eMealController@eMealAddProducts')->name('emeal.products-add');
    Route::get('/emeal/products/loadInfo', 'eMealController@eMealProductsStore')->name('emeal.products-store');

    Route::get('/emeal/recipes', 'eMealController@eMealRecipes')->name('emeal.recipes');
    Route::get('/emeal/recipes', 'eMealController@index')->name('emeal.recipes');
    Route::get('/emeal/recipes/create', 'eMealController@create')->name('emeal.recipes-create');
    Route::post('/emeal/recipes', 'eMealController@store')->name('emeal.recipes-store');
    Route::get('/emeal/recipes/{recipe}', 'eMealController@show')->name('emeal.recipes-show');
    Route::get('/emeal/recipes/{recipe}/edit', 'eMealController@edit')->name('emeal.recipes-edit');
    Route::put('/emeal/recipes/{recipe}', 'eMealController@update')->name('emeal.recipes-update');
    Route::delete('/emeal/recipes/{recipe}', 'eMealController@destroy')->name('emeal.recipes-destroy');
    Route::post('/emeal/recipes/{recipe}/addProduct', 'eMealController@addProductsToRecipe')->name('emeal.recipes-addProduct');
    Route::post('/emeal/recipes/{recipe}/update-product', 'eMealController@updateProduct')->name('emeal.recipes-update-product');
    Route::get('/emeal/recipes/loadInfo', 'eMealController@eMealProductsRecipeStore')->name('emeal.products-recipe-store');

	//Ranking
	Route::get('/ranking', 'RankingController@getIndex')->name('ranking.get-index');

	//Notebook
	Route::get('/notebook' , 'NotebookController@getIndex')->name('notebook.get-index');
	Route::post('/notebook', 'NotebookController@store')->name('notebook.store');
	Route::delete('/notebook/{id}', 'NotebookController@destroy')->name('notebook.destroy');
});