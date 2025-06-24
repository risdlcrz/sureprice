<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectTimelineController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\MaterialController;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Supplier;
use App\Http\Controllers\PurchaseRequestApprovalController;
use App\Http\Controllers\PurchaseOrderController;

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

// Project Timeline Routes
Route::prefix('project-timeline')->group(function () {
    Route::get('/events', [ProjectTimelineController::class, 'apiEvents'])->name('api.project-timeline.events');
    Route::post('/events', [ProjectTimelineController::class, 'store'])->name('api.project-timeline.events.store');
});

Route::get('/materials/search', function (Request $request) {
    $query = $request->input('query');
    $materials = Material::where('name', 'like', '%' . $query . '%')
                        ->orWhere('code', 'like', '%' . $query . '%')
                        ->with('suppliers') // Eager load suppliers
                        ->get();
    return response()->json($materials);
});

// Contract Routes
Route::prefix('contracts')->group(function () {
    Route::get('/timeline', [ContractController::class, 'timeline'])->name('api.contracts.timeline');
    Route::get('/search', [ContractController::class, 'search'])->name('api.contracts.search');
    Route::get('/{contract}/items', [ContractController::class, 'getItems']);
});

// Material Routes
Route::get('/materials/test', [MaterialController::class, 'test']);
Route::get('/materials/search', [MaterialController::class, 'search']);

// Categories Test Route
Route::get('/categories/test', function() {
    $categories = \App\Models\Category::all();
    return response()->json([
        'count' => $categories->count(),
        'categories' => $categories
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/materials/test', [MaterialController::class, 'test']);
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

Route::middleware('api')->group(function () {
    Route::get('/materials/suppliers', function (Request $request) {
        try {
            $materialName = $request->query('name');
            
            if (empty($materialName)) {
                return response()->json([]);
            }
            
            // Find the material by name
            $material = Material::where('name', 'like', '%' . $materialName . '%')->first();
            
            if (!$material) {
                return response()->json([]);
            }
            
            // Get suppliers for this material, prioritizing preferred suppliers
            $suppliers = $material->suppliers()
                ->orderBy('material_supplier.is_preferred', 'desc')
                ->select('suppliers.id', 'suppliers.name')
                ->get();
            
            return response()->json($suppliers);
        } catch (\Exception $e) {
            \Log::error('Error fetching suppliers: ' . $e->getMessage());
            return response()->json([], 500);
        }
    });
});

Route::get('users/{id}', [App\Http\Controllers\UserController::class, 'showMinimal']);

Route::get('materials/{material}/suppliers', [App\Http\Controllers\MaterialController::class, 'getSuppliersForMaterial']);

// Purchase Request Approval Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/purchase-requests/{purchaseRequest}/admin-approve', [PurchaseRequestApprovalController::class, 'adminApprove'])
        ->middleware('role:admin');
    Route::post('/purchase-requests/{purchaseRequest}/supplier-approve', [PurchaseRequestApprovalController::class, 'supplierApprove'])
        ->middleware('role:supplier');
    Route::get('/purchase-requests/{purchaseRequest}/approval-status', [PurchaseRequestApprovalController::class, 'getApprovalStatus']);
});

// Purchase Order Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/purchase-orders/{purchaseOrder}/validate-client-payment', [PurchaseOrderController::class, 'validateClientPayment'])
        ->middleware('role:admin');
    Route::post('/purchase-orders/{purchaseOrder}/validate-supplier-payment', [PurchaseOrderController::class, 'validateSupplierPayment'])
        ->middleware('role:supplier');
    Route::post('/purchase-orders/{purchaseOrder}/confirm-delivery', [PurchaseOrderController::class, 'confirmDelivery'])
        ->middleware('role:admin');
    Route::get('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'getStatus']);
});

require __DIR__.'/auth.php'; 