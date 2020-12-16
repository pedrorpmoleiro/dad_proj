<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;
use App\Models\Customer;
/* *** TESTS *** */

use App\Models\Order;

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
    /* *** Auth Routes *** */
    Route::prefix('auth')->group(function () {
        // Logout Route
        Route::post('logout', [AuthController::class, 'logout'])->name("user.logout");

        // Resend Email Verification
        Route::get('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name("verification.send");

        // Verify Email
        Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name("verification.verify");
    });

    /* *** User Routes *** */
    Route::prefix('users')->group(function () {
        // Get current user data
        Route::get('me', [UserController::class, 'me'])->name("user.get_auth_user_info");

        // Update User Data
        Route::post('update', [UserController::class, 'updateUserData'])->name("user.update_auth_user_info");

        // Update User Password
        Route::post('update/password', [UserController::class, 'updatePassword'])->name("user.update_auth_user_password");
    });

    Route::prefix('products')->middleware('manager')->group(function () {
        // Create new Product
        Route::post('new', [ProductController::class, 'create'])->name("product.create_new");

        // Update a Product
        Route::put('update', [ProductController::class, 'update'])->name("product.update");

        // Delete a Product
        Route::delete('delete', [ProductController::class, 'delete'])->name("product.delete");
    });

    Route::middleware('customer')->group(function () {
        // Update Customer Data
        Route::post('customers/update', [UserController::class, 'updateCustomerData'])->name("customer.update_info");

        Route::prefix('orders')->group(function () {
            // Create new Order
            Route::post('new', [OrderController::class, 'create'])->name("order.new");

            // Get Customer Orders
            Route::get('', [OrderController::class, 'getCustomerOrders'])->name("order.get_all");

            // Get Customer Open Orders
            Route::get('open', [OrderController::class, 'getCustomerOpenOrders'])->name("order.get_open");

            // Get Customer Order History
            Route::get('history', [OrderController::class, 'getCustomerOrderHistory'])->name("order.get_history");
        });
    });
});

/* *** Unprotected Routes *** */
/* *** Auth Routes *** */
Route::prefix('auth')->group(function () {
    // User Login Route
    Route::post('login', [AuthController::class, 'login'])->name("user.login");

    // TODO Reset Password
    // https://laravel.com/docs/8.x/passwords#requesting-the-password-reset-link
});

// Customer Register Route
Route::post('customers/register', [AuthController::class, 'registerCustomer'])->name("customer.register");
// Get all products Route
Route::get('products', [ProductController::class, 'all'])->name("product.get_all");

/* !!! TESTING ROUTE !!! */
Route::get('tests', function () {
    // dd(date(env('INPUT_FORMAT_DATE') . ' ' . env('INPUT_FORMAT_TIME_SECONDS')));
    $response = Order::find(14);
    // $response = Customer::find(22);
    // $response->items;
    $response->customer;
    $response->cook;
    $response->delivery_man;
    // $response = $response->orders()->whereIn('status', ['C', 'D'])->get();
    // $response = $response->orders()->whereNotIn('status', ['C', 'D'])->get();
    return response()->json($response);
})->name("tests");
