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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/* JWt route */

//Route for authentification
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

//Route for logout or refresh tokens
Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', 'AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh');
});

// Routes for CRUD operations on roles
Route::middleware('jwt.auth')->group(function () {
    Route::get('role', 'RoleController@index');
    Route::get('role/{id}', 'RoleController@show');
    Route::post('role', 'RoleController@store');
    Route::put('role/{id}', 'RoleController@update');
    Route::delete('role/{id}', 'RoleController@destroy');
    Route::delete('/role/{id}', 'RoleController@destroy')->middleware('auth.jwt', 'check.role:3');
});

// Routes for CRUD operations on users
Route::middleware('jwt.auth')->group(function () {
    Route::get('user', 'UserController@index');
    Route::get('user/{id}', 'UserController@show');
    Route::post('user', 'UserController@store');
    Route::put('user/{id}', 'UserController@update');
    Route::delete('/user/{id}', 'UserController@destroy')->middleware('auth.jwt', 'check.role:3');
});
