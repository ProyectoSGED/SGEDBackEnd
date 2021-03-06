<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/Amenazas','AmenazasController@index')->name('Amenazas');
route::get('/Listadoshape','ListadoshapeController@index')->name('Listadoshape');
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', 'MessagesController@store');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');