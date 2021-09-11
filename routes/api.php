<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WishlistController;
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

// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
// header('Access-Control-Allow-Origin: *');

// User Routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api'])->group(function () {

    Route::apiResource('user/orders', OrderController::class);
    Route::apiResource('user/order/histories', OrderHistoryController::class);
    Route::apiResource('user/carts', CartController::class);
    Route::get('get/total', [CartController::class, 'gettotal']);
    Route::get('user/clear/cart', [CartController::class, 'destroyall']);
    Route::apiResource('user/transactions', TransactionController::class);
    Route::get('user/profile', [UserController::class, 'viewProfile'])->name('profile.user');


    Route::get('user/notifications', [NotificationController::class, 'getnotifications']);
    Route::get('user/notifications/unread', [NotificationController::class, 'unreadnotifications']);
    Route::get('user/notifications/mark', [NotificationController::class, 'markreadnotifications']);
    Route::get('user/notifications/{id}/mark', [NotificationController::class, 'marksinglenotification']);
    Route::delete('user/notifications/delete', [NotificationController::class, 'destroy']);
});
//Store routes
Route::apiResource('stores', StoreController::class);

//Wishlist routes
Route::apiResource('wishlists', WishlistController::class);

//Story routes
Route::apiResource('stories', StoryController::class);
Route::get('removestories', [StoryController::class, 'remove']);


// Registration routes

Route::post('users/register', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::get('users/logout', [UserController::class, 'logout'])->name('logout.user');
Route::post('users/forgot-password', [UserController::class, 'postEmail']);
Route::post('users/update-password', [UserController::class, 'updatePassword']);
Route::apiResource('otp', OtpController::class);




Route::post('vendor/register', [VendorController::class, 'register']);
Route::post('admin/register', [AdminController::class, 'register']);

// Vendor Routes
Route::middleware('auth:vendor')->get('/vendor', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:vendor'])->group(function () {
});


// Admin routes
Route::middleware('auth:admin')->get('/admin', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:admin'])->group(function () {
});


// Social Login routes
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::post('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
