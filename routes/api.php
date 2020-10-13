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
    Route::get('/users/verify-change-password', 'UserController@verifyChangeUserPassword');

    Route::get('/profiles', 'ProfileController@index');

    Route::put('/shapes/update', "ShapeController@update");
    Route::delete('/shapes/delete', "ShapeController@destroy");
    Route::get('/shapes/list/all', "ShapeController@index");
    Route::post('/shapes/new', "ShapeController@store");
    Route::get('/shapes/file', 'ShapeController@downloadShape');
    Route::get('/shapes/get', "ShapeController@getShapeById");
    Route::get('/shapes/list', 'ShapeController@shapesByCategory');
    Route::get('/shapes/categories', 'CategoriaShapeController@index');
    Route::get('/shapes/search', 'ShapeController@findShapeByQuery');
    Route::get('/shapes/categories/all', 'ShapeController@getShapesCategories');

    Route::post('/contact', "ContactController@sendContactMessage");
    Route::post('/password-reset', "AuthController@sendResetPasswordEmail");
    Route::post('/password-change', "AuthController@changePassword");
});


Route::middleware(['api'])->group(function ($router) {
    Route::post('/signin', "AuthController@signin");
    Route::get('/signout', "AuthController@signout");
});
