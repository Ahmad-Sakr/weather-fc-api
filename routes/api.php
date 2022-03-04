<?php

use App\Http\Controllers\api\v1\CityController;
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

});
