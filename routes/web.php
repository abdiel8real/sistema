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

Route::get('main', function () {
    return view('contenido/contenido');
})->name('main');


Route::get("categoria", "CategoriaController@index");
Route::post("categoria/registrar", "CategoriaController@store");
Route::put("categoria/actualizar", "CategoriaController@update");
Route::put("categoria/desactivar", "CategoriaController@desactivar");
Route::put("categoria/activar", "CategoriaController@activar");

Route::get("articulo", "ArticuloController@index");
Route::post("articulo/registrar", "ArticuloController@store");
Route::put("articulo/actualizar", "ArticuloController@update");
Route::put("articulo/desactivar", "ArticuloController@desactivar");
Route::put("articulo/activar", "ArticuloController@activar");

Route::get("cliente", "ClienteController@index");
Route::post("cliente/registrar", "ClienteController@store");
Route::put("cliente/actualizar", "ClienteController@update");

Route::get("proveedor", "ProveedorController@index");
Route::post("proveedor/registrar", "ProveedorController@store");
Route::put("proveedor/actualizar", "ProveedorController@update");

Route::get('/', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');

Route::get('/home', 'HomeController@index')->name('home');
