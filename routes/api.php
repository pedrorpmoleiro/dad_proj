<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Api\UserController;

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

// Login Route
Route::post('login', [AuthController::class, 'login'])->name("login");

// SANCTUM Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Logout Route
    Route::post('logout', [AuthController::class, 'logout'])->name("logout");

    // User Routes
    Route::prefix('users')->group(function () {
        // Get current user data
        Route::get('me', [UserController::class, 'me'])->name("getCurrentUser");
    });
});

Route::get('products', [ProductController::class, 'getProducts'])->name("getProducts");

