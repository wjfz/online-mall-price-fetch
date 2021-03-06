<?php

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

Route::get('/home/{sku?}/{result?}', 'HomeController@index')->name('home');

Route::get('/addAmazonSku/{sku}', 'HomeController@addAmazonSku')->name('addAmazonSku');
Route::post('/addSku', 'HomeController@addSku')->name('addSku');
