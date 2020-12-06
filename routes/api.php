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

    // User Routes
    Route::prefix('users')->group(function () {
        // Get current user data
        Route::get('me', [UserController::class, 'me'])->name("user.get_info");
    });
});

/* *** Unprotected Routes *** */
// User Login Route
Route::post('login', [AuthController::class, 'login'])->name("user.login");
// Login Route
Route::post('register', [AuthController::class, 'registerCustomer'])->name("customer.register");

// Get all products Route
Route::get('products', [ProductController::class, 'getProducts'])->name("product.get_all");


/* !!! TESTING ROUTE !!! */
Route::get('tests', function () {
    $response = Order::find(1);

    /*foreach ($response->items as $item) {
        $item->pivot->quantity;
        $item->pivot->unit_price;
        $item->pivot->sub_total_price;
    }*/
    $response->items;

    // $response = Customer::find(22);
    // $response->orders;
    return response()->json($response);
})->name("tests");
