<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ManagerController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;

/* *** TESTS *** */

use App\Models\User;

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
        // Get all users
        Route::get('/', [UserController::class, 'all'])->name("user.get_all");

        // Get current user data
        Route::get('me', [UserController::class, 'me'])->name("user.get_auth_user_info");

        // Update User Data
        Route::patch('update', [UserController::class, 'updateUserData'])->name("user.update_auth_user_info");

        // Update User Password
        Route::patch('update/password', [UserController::class, 'updatePassword'])->name("user.update_auth_user_password");

        // Upload User Photo
        Route::post('upload/photo', [UserController::class, 'uploadPhoto'])->name("user.upload_photo");
    });

    Route::middleware('manager')->group(function () {
        Route::prefix('products')->group(function () {
            // Create new Product
            Route::post('new', [ProductController::class, 'create'])->name("product.create_new");

            // Update a Product
            Route::post('update', [ProductController::class, 'update'])->name("product.update");

            Route::delete('delete/{id}', [UserController::class, 'delete'])->name("user.delete");

            // Delete a Product
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name("product.delete");
        });

        // Get all Employees
        Route::get('employees', [ManagerController::class, 'getEmployees'])->name("manager.get_employees");

        Route::prefix('orders')->group(function () {
            // Get all open Orders
            Route::get('open', [ManagerController::class, 'getOpenOrders'])->name("manager.get_open_orders");

            // Cancel order
            Route::patch('cancel/{id}', [OrderController::class, 'cancelOrder'])->name("manager.cancel_order");

            // Assign Order to Cook
            Route::patch('cook/assign', [OrderController::class, 'assignOrderToCook'])->name("manager.assing_to_cook");
        });

        Route::prefix('users')->group(function () {
            // Create User
            Route::post('create', [UserController::class, 'create'])->name("user.create");

            // Delete User
            Route::delete('delete/{id}', [UserController::class, 'delete'])->name("user.delete");

            // Block or Unblock a User
            Route::patch('block/{id}', [UserController::class, 'block'])->name("user.block");

            // Patch User Data (manager)
            Route::patch('patch/{id}', [UserController::class, 'patchUserData'])->name("user.patch_user_info");
        });
    });

    Route::middleware('customer')->group(function () {
        // Update Customer Data
        Route::patch('customers/update', [UserController::class, 'updateCustomerData'])->name("customer.update_info");

        Route::prefix('orders')->group(function () {
            // Create new Order
            Route::post('new', [OrderController::class, 'create'])->name("order.new");

            // Get Customer Orders
            Route::get('customer', [OrderController::class, 'getCustomerOrders'])->name("order.get_all");

            // Get Customer Open Orders
            Route::get('customer/open', [OrderController::class, 'getCustomerOpenOrders'])->name("order.get_open");

            // Get Customer Order History
            Route::get('customer/history', [OrderController::class, 'getCustomerOrderHistory'])->name("order.get_history");
        });
    });

    // Get a specific Order
    Route::get('orders/{id}', [OrderController::class, 'getOrder'])->name("order.get_order");

    /* *** Cook Protected Routes *** */
    Route::middleware('cook')->prefix('cook')->group(function () {
        // Get the current Order for the logged in Cook
        Route::get('order', [OrderController::class, 'getCurrentCookOrder'])->name("cook.current_order");

        // Set Order Prepared
        Route::patch('order/prepared', [OrderController::class, 'setOrderPrepared'])->name("cook.set_current_prepared");
    });

    /* *** DeliveryMan Protected Routes *** */
    Route::middleware('delivery_man')->prefix('deliveryman')->group(function () {
        // Get the current Order being delivered by the delivery man or a list of available Orders ready to deliver
        Route::get('orders', [OrderController::class, 'getDeliverymanOrders'])->name("delivery_man.get_orders");

        // Change the status of an Order ready to deliver to 'in transit'
        Route::patch('orders/transit/{id}', [OrderController::class, 'setOrderInTransit'])->name("delivery_man.set_order_in_transit");

        // Change the status of an Order to Delivered
        Route::patch('orders/delivered', [OrderController::class, 'setOrderDelivered'])->name("delivery_man.set_order_delivered");
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

Route::prefix('products')->group(function () {
    // Get all products Route
    Route::get('/', [ProductController::class, 'all'])->name("product.get_all");

    // Get one specific product
    Route::get('get/{id}', [ProductController::class, 'getProduct'])->name("product.get");
});

/* !!! TESTING ROUTE !!! */
Route::get('tests', function () {
    return response()->json(Order::where('status', 'T')->where('delivered_by', 10)->get());
})->name("tests");
