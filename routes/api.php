<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\PurchaseOrderController;

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

// Health check endpoint for monitoring
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'service' => 'Stock Management API'
    ]);
});

Route::prefix('v1')->group(function () {

    // Public routes (Authentication) - Rate limited to 5 attempts per minute
    Route::middleware(['throttle:auth'])->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Protected routes (require authentication)
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        // Auth routes (accessible par tous les utilisateurs authentifiés)
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // Routes READ-ONLY pour tous les rôles (viewer, user, manager, admin)
        Route::middleware(['role:admin,manager,user,viewer'])->group(function () {
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::get('/categories/{category}', [CategoryController::class, 'show']);
            Route::get('/suppliers', [SupplierController::class, 'index']);
            Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{product}', [ProductController::class, 'show']);
            Route::get('/stock-movements', [StockMovementController::class, 'index']);
            Route::get('/stock-movements/{stock_movement}', [StockMovementController::class, 'show']);
            Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
            Route::get('/purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'show']);
        });

        // Routes CREATE pour user, manager et admin
        Route::middleware(['role:admin,manager,user'])->group(function () {
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::post('/suppliers', [SupplierController::class, 'store']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::post('/stock-movements', [StockMovementController::class, 'store']);
            Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
            Route::post('/purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive']);
        });

        // Routes UPDATE pour manager et admin
        Route::middleware(['role:admin,manager'])->group(function () {
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::patch('/categories/{category}', [CategoryController::class, 'update']);
            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
            Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);
            Route::put('/products/{product}', [ProductController::class, 'update']);
            Route::patch('/products/{product}', [ProductController::class, 'update']);
            Route::put('/stock-movements/{stock_movement}', [StockMovementController::class, 'update']);
            Route::patch('/stock-movements/{stock_movement}', [StockMovementController::class, 'update']);
            Route::put('/purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'update']);
            Route::patch('/purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'update']);
        });

        // Routes DELETE uniquement pour admin
        Route::middleware(['role:admin'])->group(function () {
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']);
            Route::delete('/stock-movements/{stock_movement}', [StockMovementController::class, 'destroy']);
            Route::delete('/purchase-orders/{purchase_order}', [PurchaseOrderController::class, 'destroy']);
        });
    });

});
