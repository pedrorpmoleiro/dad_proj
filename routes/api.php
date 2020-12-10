<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;

/* *** TESTS *** */

use App\Models\Order;
use App\Models\Customer;

/* ***  END  *** */

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

/* *** SANCTUM Protected Routes *** */

Route::middleware('auth:sanctum')->group(function () {
    // Logout Route
    Route::post('logout', [AuthController::class, 'logout'])->name("user.logout");

    /* *** User Routes *** */
    Route::prefix('users')->group(function () {
        // Get current user data
        Route::get('me', [UserController::class, 'me'])->name("user.get_auth_user_info");

        // Update User Data
        Route::post('update', [UserController::class, 'updateUserData'])->name("user.update_auth_user_info");

        // Update User Password
        Route::post('update/password', [UserController::class, 'updatePassword'])->name("user.update_auth_user_password");
    });

    Route::middleware('customer')->group(function () {
        // Update Customer Data
        Route::post('customers/update', [UserController::class, 'updateCustomerData'])->name("customer.update_info");

        // Create new Order
        Route::post('orders/new', [OrderController::class, 'create'])->name("order.new");
    });
});

/* *** Unprotected Routes *** */
// User Login Route
Route::post('login', [AuthController::class, 'login'])->name("user.login");
// Customer Register Route
Route::post('customers/register', [AuthController::class, 'registerCustomer'])->name("customer.register");
// Get all products Route
Route::get('products', [ProductController::class, 'getProducts'])->name("product.get_all");

/* !!! TESTING ROUTE !!! */
Route::get('tests', function () {
    dd(date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS')));
    $response = Order::find(1);
    $response->items;
    return response()->json($response);
})->name("tests");
