<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
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
});

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

// Registration routes

Route::post('users/register', [UserController::class, 'register']);
Route::get('users/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
Route::post('users/login', [UserController::class, 'login']);
Route::get('users/logout', [UserController::class, 'logout'])->name('logout.user');




Route::post('vendor/register', [VendorController::class, 'register']);
Route::post('admin/register', [AdminController::class, 'register']);

Route::apiResource('orders', OrderController::class);
Route::apiResource('carts', CartController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('otp', OtpController::class);

// Social Login routes
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::post('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
