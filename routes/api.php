<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors']], function () {
    Route::get('/users/list', 'UserController@index');
    Route::get('/users/get', 'UserController@getUserById');
    Route::post('/users/new', 'UserController@store');
    Route::put('/users/update', 'UserController@update');
    Route::delete('/users/deactivate', 'UserController@deactivateUser');

    Route::get('/profiles', 'ProfileController@index');

    Route::post('/shapes/new', "ShapeController@store");
    Route::get('/shapes/file', 'ShapeController@downloadShape');
    Route::get('/shapes/list', 'ShapeController@shapesByCategory');
    Route::get('/shapes/categories', 'CategoriaShapeController@index');
});
