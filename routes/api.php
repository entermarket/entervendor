<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SocialLoginController;
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

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

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

Route::post('user/register', [UserController::class, 'register']);

Route::post('vendor/register', [VendorController::class, 'register']);

Route::post('admin/register', [AdminController::class, 'register']);


// Social Login routes
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);
