<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\MaterialController;

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

Route::get('/materials/search', [MaterialController::class, 'search']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/contracts/timeline', [ContractController::class, 'timeline']);
    Route::get('/contracts/search', [ContractController::class, 'search']);
    Route::get('/materials/test', [MaterialController::class, 'test']);
    Route::get('/categories/test', function() {
        $categories = \App\Models\Category::all();
        return response()->json([
            'count' => $categories->count(),
            'categories' => $categories
        ]);
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware', 'inertia')
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('purchase-requests/{purchaseRequest}/items', [PurchaseRequestController::class, 'getItems']);

require __DIR__.'/auth.php'; 