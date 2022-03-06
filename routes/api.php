<?php

use App\Http\Controllers\api\v1\CityController;
use App\Http\Controllers\api\v1\RecordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| v1
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function() {

    /*
    |--------------------------------------------------------------------------
    | 01. Cities
    |--------------------------------------------------------------------------
    */
    Route::resource('cities', CityController::class);

    /*
    |--------------------------------------------------------------------------
    | 02. Store Records
    |--------------------------------------------------------------------------
    */
    Route::post('/records', [RecordController::class, 'store'])->name('weather.store');

    /*
    |--------------------------------------------------------------------------
    | 03. Forecast
    |--------------------------------------------------------------------------
    */
    Route::get('/forecast', [RecordController::class, 'index'])->name('weather.forecast');

});
