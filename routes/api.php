<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PromoController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('clients', [ClientController::class, 'index']);
Route::get('clients/history/{id}', [ClientController::class, 'history']);
Route::get('clients/{id}', [ClientController::class, 'show']);
Route::post('login', [ClientController::class, 'login']);
Route::post('register', [ClientController::class, 'register']);

Route::get('products', [ProductController::class, 'index']);
Route::post('products/create', [ProductController::class, 'addProduct']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);
Route::get('products/size/{id}', [ProductController::class, 'size']);
Route::post('buy/{client_id}/{product_id}', [ProductController::class, 'buy']);


Route::get('reviews', [ReviewController::class, 'index']);
Route::post('reviews/create', [ReviewController::class, 'create']);

Route::get('promo', [PromoController::class, 'index']);
Route::get('promo/{id}', [PromoController::class, 'show']);
Route::post('promo/create', [PromoController::class, 'create']);
