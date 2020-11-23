<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginControllerAPI;
use App\Http\Controllers\ProductControllerAPI;

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

Route::post('login', [LoginControllerAPI::class, 'login'])->name("login");
Route::middleware('auth:api')->post('logout', [LoginControllerAPI::class, 'logout'])->name("logout");

Route::get('products', [ProductControllerAPI::class, 'getProducts'])->name("getProducts");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
