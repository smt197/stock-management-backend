<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockMovementController;

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

// Public API routes (you may want to add authentication later)
Route::prefix('v1')->group(function () {

    // Categories routes
    Route::apiResource('categories', CategoryController::class);

    // Suppliers routes
    Route::apiResource('suppliers', SupplierController::class);

    // Products routes
    Route::apiResource('products', ProductController::class);

    // Stock Movements routes
    Route::apiResource('stock-movements', StockMovementController::class);

});
