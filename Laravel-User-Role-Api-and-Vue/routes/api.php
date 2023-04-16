<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);

//Route for logout or refresh tokens
Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout',  [AuthController::class,'logout']);
    Route::post('/refresh', [AuthController::class,'refresh']);
});

// Routes for CRUD operations on roles
Route::middleware('jwt.auth')->group(function () {
    Route::get('role', [RoleController::class,'index']);
    Route::get('role/{id}', [RoleController::class,'show']);
    Route::post('role', [RoleController::class,'store']);
    Route::put('role/{id}', [RoleController::class,'update']);
    // Route::delete('role/{id}', [RoleController::class,'destroy']);
    Route::delete('/role/{id}', [RoleController::class,'destroy'])->middleware('jwt.auth', 'checkrole:3');
});

// Routes for CRUD operations on users
Route::middleware('jwt.auth')->group(function () {
    Route::get('user', [UserController::class,'index']);
    Route::get('user/{id}', [UserController::class,'show']);
    Route::post('user', [UserController::class,'store']);
    Route::put('user/{id}', [UserController::class,'update']);
    Route::delete('/user/{id}', [UserController::class,'destroy'])->middleware('jwt.auth', 'checkrole:3');
});



// //Routes for authentification
// Route::post('/login', 'AuthController@login');
// Route::post('/register', 'AuthController@register');

// //Routes for logout or refresh tokens
// Route::middleware('jwt.auth')->group(function () {
//     Route::post('/logout', 'AuthController@logout');
//     Route::post('/refresh', 'AuthController@refresh');
// });

// //Routes for CRUD operations on roles
// Route::middleware('jwt.auth')->resource('role', 'RoleController', ['except' => ['create', 'edit']])->middleware('auth.jwt', 'check.role:3');

// //Routes for CRUD operations on users
// Route::middleware('jwt.auth')->resource('user', 'UserController', ['except' => ['create', 'edit']])->middleware('auth.jwt', 'check.role:3');

