<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginControllerAPI;

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

Route::post('login', [LoginControllerAPI::class, 'login']);
Route::middleware('auth:api')->post('logout', [LoginControllerAPI::class, 'logout']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
