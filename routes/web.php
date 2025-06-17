<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ClientMiddleware;
use App\Http\Controllers\CompanyDocumentController;
use App\Http\Controllers\InformationManagementController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierInvitationController;
use App\Http\Controllers\BudgetAllocationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierRankingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectTimelineController;
use App\Http\Controllers\WarrantyRequestController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\Supplier\SupplierMaterialController;
use App\Http\Controllers\Supplier\SupplierDashboardController;
use App\Http\Controllers\Supplier\SupplierQuotationController;
use App\Http\Controllers\Warehouse\WarehouseDashboardController;
use App\Http\Controllers\Warehouse\WarehouseInventoryController;
use App\Http\Controllers\Warehouse\WarehouseDeliveryController;
use App\Http\Controllers\Warehouse\WarehouseReportController;
use App\Http\Controllers\Procurement\ProcurementDashboardController;
use App\Http\Controllers\Procurement\ProcurementRfqController;
use App\Http\Controllers\Procurement\ProcurementOrderController;
use App\Http\Controllers\Procurement\ProcurementSupplierController;

// Home route redirect to login
Route::get('/', function () {
    return redirect()->route('login.form');
});

// ================== Authentication Routes ==================
Route::middleware('web')->group(function () {
    require __DIR__.'/auth.php';
});

// Show Login Form
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login.form');

// Handle Login Submission
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

// Handle Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ================== Registration Routes ==================
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Removed employee registration route
Route::post('/register/company', [RegisteredUserController::class, 'store'])->name('register.company');

// Auth required routes
Route::middleware(['auth'])->group(function () {
    // Pending approval route
    Route::get('/pending-approval', function () {
        return view('auth.pending-approval');
    })->name('pending.approval');

    // Rejected account route
    Route::get('/account-rejected', function () {
        return view('auth.account-rejected');
    })->name('account.rejected');

    // Project Dashboard
    Route::get('/project-dashboard', [ProjectController::class, 'dashboard'])->name('admin.project');

    // Contract Dashboard
    Route::get('/contract-dashboard', [ContractController::class, 'dashboard'])->name('admin.contract');

    // Contract Routes
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('/create', [ContractController::class, 'create'])->name('create');
        Route::get('/clear-session', [ContractController::class, 'clearContractSession'])->name('clear-session');
        Route::get('/step1', [ContractController::class, 'step1'])->name('step1');
        Route::post('/step1', [ContractController::class, 'storeStep1'])->name('store.step1');
        Route::post('/step1/save', [ContractController::class, 'saveStep1'])->name('save.step1');
        Route::get('/step2', [ContractController::class, 'step2'])->name('step2');
        Route::post('/step2', [ContractController::class, 'storeStep2'])->name('store.step2');
        Route::post('/step2/save', [ContractController::class, 'saveStep2'])->name('save.step2');
        Route::get('/step3', [ContractController::class, 'step3'])->name('step3');
        Route::post('/step3', [ContractController::class, 'storeStep3'])->name('store.step3');
        Route::post('/step3/save', [ContractController::class, 'saveStep3'])->name('save.step3');
        Route::get('/step4', [ContractController::class, 'step4'])->name('step4');
        Route::post('/', [ContractController::class, 'store'])->name('store');
        Route::get('/{contract}', [ContractController::class, 'show'])->name('show');
        Route::get('/{contract}/download', [ContractController::class, 'download'])->name('download');
        Route::patch('/{contract}/status', [ContractController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{contract}/status', [ContractController::class, 'updateStatus']);
        Route::post('/save-signature', [ContractController::class, 'saveSignature'])->name('contracts.save.signature');
        Route::post('/contracts/store/step3', [ContractController::class, 'storeStep3'])->name('contracts.store.step3');
        Route::post('/contracts/save/step3', [ContractController::class, 'saveStep3'])->name('contracts.save.step3');
        Route::get('/contracts/step4', [ContractController::class, 'step4'])->name('contracts.step4');
        Route::post('/contracts/store/step4', [ContractController::class, 'storeStep4'])->name('contracts.store.step4');
        Route::post('/save/step4', [ContractController::class, 'saveStep4'])->name('save.step4');
    });
    
    // Supporting routes for contract form
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/materials/search', [MaterialController::class, 'search'])->name('materials.search');
    Route::get('/materials/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('materials.suppliers');

    // Material Routes
    Route::prefix('materials')->name('materials.')->group(function () {
        Route::get('/', [MaterialController::class, 'index'])->name('index');
        Route::get('/create', [MaterialController::class, 'create'])->name('create');
        Route::post('/', [MaterialController::class, 'store'])->name('store');
        Route::get('/{material}', [MaterialController::class, 'show'])->name('show');
        Route::get('/{material}/edit', [MaterialController::class, 'edit'])->name('edit');
        Route::put('/{material}', [MaterialController::class, 'update'])->name('update');
        Route::delete('/{material}', [MaterialController::class, 'destroy'])->name('destroy');
        Route::get('/search', [MaterialController::class, 'search'])->name('search');
        Route::get('/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('suppliers');
        Route::post('/update-srp', [MaterialController::class, 'updateSrpPrices'])->name('update-srp');
        Route::post('/ajax-store', [MaterialController::class, 'ajaxStore'])->name('ajax-store');
        Route::get('/check-code', [MaterialController::class, 'checkCode'])->name('check-code');
        Route::get('/{material}/transactions', [MaterialController::class, 'transactions'])->name('materials.transactions');
    });

    // API Material Routes
    Route::prefix('api/materials')->name('api.materials.')->group(function () {
        Route::get('/search', [MaterialController::class, 'apiSearch'])->name('search');
        Route::get('/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('suppliers');
        Route::get('/all', [MaterialController::class, 'getAllMaterials'])->name('all');
        Route::get('/search-by-supplier', [MaterialController::class, 'searchBySupplier'])->name('search-by-supplier');
    });

    // Inquiry Routes
    Route::resource('inquiries', InquiryController::class);
    Route::post('/api/inquiries/{inquiry}/remove-attachment', [InquiryController::class, 'removeAttachment']);
    Route::get('/api/inquiries/search', [InquiryController::class, 'search'])->name('inquiries.search');

    // Quotation Routes
    Route::resource('quotations', QuotationController::class);
    Route::post('/api/quotations/{quotation}/send', [QuotationController::class, 'send']);
    Route::post('/api/quotations/remove-attachment', [QuotationController::class, 'removeAttachment']);
    Route::get('/api/quotations/search', [QuotationController::class, 'search'])->name('quotations.search');
    Route::get('/quotations/attachment/{attachment}/download', [QuotationController::class, 'downloadAttachment'])->name('quotations.attachment.download');
    Route::get('/quotations/response/attachment/{attachment}/download', [QuotationController::class, 'downloadResponseAttachment'])->name('quotations.response.attachment.download');

    // Invitation Routes
    Route::resource('supplier-invitations', SupplierInvitationController::class);
    Route::post('/api/supplier-invitations/{invitation}/resend', [SupplierInvitationController::class, 'resend']);
    Route::post('/api/supplier-invitations/remove-attachment', [SupplierInvitationController::class, 'removeAttachment']);
    Route::get('/api/supplier-invitations/search', [SupplierInvitationController::class, 'search'])->name('supplier-invitations.search');

    // Supplier Routes
    Route::resource('suppliers', SupplierController::class);
    Route::get('/admin/suppliers/search', [SupplierController::class, 'search'])->name('admin.suppliers.search');

    // Purchase Requests
    Route::resource('purchase-requests', PurchaseRequestController::class);
    Route::get('/api/purchase-requests/{purchaseRequest}/items', [PurchaseRequestController::class, 'getItems'])->name('api.purchase-requests.items');
    Route::post('/purchase-requests/generate-from-contract', [App\Http\Controllers\PurchaseRequestController::class, 'generateFromContract'])->name('purchase-requests.generate-from-contract');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/{id}/json', [App\Http\Controllers\PurchaseOrderController::class, 'showJson'])->name('purchase-orders.json');

    // Transaction Routes
    Route::get('/transactions/past', [\App\Http\Controllers\TransactionController::class, 'pastTransactions'])->name('transactions.past');
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class);

    // Add procurement routes
    Route::get('/admin/procurement', [ProcurementController::class, 'index'])->name('admin.procurement');

    // Supplier Rankings Routes
    Route::prefix('admin/suppliers')->name('suppliers.')->middleware(['auth'])->group(function () {
        Route::get('rankings', [AnalyticsController::class, 'supplierRankings'])->name('rankings');
        Route::get('template/download', [SupplierRankingController::class, 'downloadTemplate'])->name('template.download');
        Route::get('materials/template/download', [SupplierRankingController::class, 'downloadMaterialsTemplate'])->name('materials.template.download');
        Route::post('{supplier}/evaluations', [SupplierRankingController::class, 'storeEvaluation'])->name('evaluations.store');
        Route::post('{supplier}/metrics', [SupplierRankingController::class, 'updateMetrics'])->name('metrics.update');
    });

    // Inventory Management Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    Route::post('/inventory/import-from-scope', [InventoryController::class, 'importFromScope'])->name('inventory.import-from-scope');
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/inventory/expiring', [InventoryController::class, 'expiring'])->name('inventory.expiring');

    // Project Timeline Route
    Route::get('/project-timeline', [ProjectTimelineController::class, 'index'])->name('project-timeline.index');
    Route::get('/project-timeline/create', [ProjectTimelineController::class, 'create'])->name('project-timeline.create');
    Route::post('/project-timeline', [ProjectTimelineController::class, 'store'])->name('project-timeline.store');
    Route::get('/project-timeline/{projectTimeline}', [ProjectTimelineController::class, 'show'])->name('project-timeline.show');
    Route::get('/project-timeline/{projectTimeline}/edit', [ProjectTimelineController::class, 'edit'])->name('project-timeline.edit');
    Route::put('/project-timeline/{projectTimeline}', [ProjectTimelineController::class, 'update'])->name('project-timeline.update');
    Route::delete('/project-timeline/{projectTimeline}', [ProjectTimelineController::class, 'destroy'])->name('project-timeline.destroy');

    // Add this route for fetching contract items (materials) for web requests
    Route::get('/contracts/{contract}/items', [\App\Http\Controllers\ContractController::class, 'getItems'])->name('contracts.items');

    // Warranty Requests Routes
    Route::prefix('warranty-requests')->name('warranty-requests.')->group(function () {
        Route::get('/', [WarrantyRequestController::class, 'index'])->name('index');
        Route::get('/export', [WarrantyRequestController::class, 'export'])->name('export');
        Route::get('/template', [WarrantyRequestController::class, 'template'])->name('template');
        Route::post('/import', [WarrantyRequestController::class, 'import'])->name('import');
        Route::post('/additional-work', [WarrantyRequestController::class, 'storeAdditionalWork'])->name('additional-work');
        Route::get('/{warrantyRequest}', [WarrantyRequestController::class, 'show'])->name('show');
        Route::post('/', [WarrantyRequestController::class, 'store'])->name('store');
        Route::post('/{warrantyRequest}/status', [WarrantyRequestController::class, 'updateStatus'])->name('update-status');
    });

    // History Dashboard Route
    Route::get('/history-dashboard', function () {
        return view('admin.history-dashboard');
    })->name('history.dashboard');

    // Warehouse Routes
    Route::middleware(['role:warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/dashboard', [WarehouseDashboardController::class, 'index'])->name('dashboard');
        Route::get('/stock-alerts', [WarehouseDashboardController::class, 'getStockAlerts'])->name('stock-alerts');
        Route::get('/stock-movements', [WarehouseDashboardController::class, 'getStockMovements'])->name('stock-movements');
        Route::resource('deliveries', WarehouseDeliveryController::class);
        Route::resource('inventory', WarehouseInventoryController::class);
        Route::resource('reports', WarehouseReportController::class);
    });
});

// Payments routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/mark-as-paid', [PaymentController::class, 'markAsPaid'])->name('payments.markAsPaid');
    Route::post('/payments/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payments.uploadProof');
    Route::post('/payments/{payment}/submit-client-proof', [PaymentController::class, 'submitClientProof'])->name('payments.submitClientProof');
    Route::post('/payments/{payment}/submit-admin-proof', [PaymentController::class, 'submitAdminProof'])->name('payments.submitAdminProof');
});

// Client Routes
Route::middleware(['auth', \App\Http\Middleware\ClientMiddleware::class])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/payments/dashboard', [PaymentController::class, 'dashboard'])->name('payments.dashboard');
    Route::get('/project-procurement', [ClientController::class, 'projectProcurement'])->name('project.procurement');
});

// ================== Email Verification Routes ==================
// **Removed duplicate route /email/verify here**

// Remove duplicate client search route
// Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');

// Admin protected routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/dbadmin', [AdminController::class, 'dashboard'])->name('admin.dbadmin');
    Route::get('/admin/companies/pending', [AdminController::class, 'pending'])->name('admin.companies.pending');
    Route::post('/admin/companies/{company}/approve', [AdminController::class, 'approve'])->name('admin.companies.approve');
    Route::post('/admin/companies/{company}/reject', [AdminController::class, 'reject'])->name('admin.companies.reject');
    Route::get('/admin/companies/{company}', [AdminController::class, 'show'])->name('admin.companies.show');
    
    // Information Management Routes
    Route::resource('information-management', InformationManagementController::class);
    Route::post('information-management/import', [InformationManagementController::class, 'import'])->name('information-management.import');
    Route::get('information-management/template/download', [InformationManagementController::class, 'template'])->name('information-management.template');

    // Quotation Approval/Rejection for Admin
    Route::post('/api/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('/api/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');

    // Purchase Request Approval/Rejection & Status Update for Admin
    Route::post('purchase-requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');
    Route::post('purchase-requests/{purchaseRequest}/status', [PurchaseRequestController::class, 'updateStatus'])->name('purchase-requests.update-status');

    // Purchase Order Status Update/Completion for Admin
    Route::post('purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::post('purchase-orders/{purchaseOrder}/complete', [PurchaseOrderController::class, 'complete'])->name('purchase-orders.complete');

    // Other admin routes
    Route::get('/notification-center', function () {
        return view('admin.notification-center');
    })->name('admin.notification');

    Route::get('/analytics-dashboard', [AnalyticsController::class, 'index'])->name('admin.analytics');

    Route::get('/supplier-rankings', [AnalyticsController::class, 'supplierRankings'])->name('admin.supplier-rankings');
    Route::get('/supplier-rankings/top', [AnalyticsController::class, 'getTopSuppliers'])->name('admin.supplier-rankings.top');

    Route::get('/purchase-order', function () {
        return view('admin.purchase-order');
    })->name('admin.purchase-order');

    Route::get('/budget-allocation', [BudgetAllocationController::class, 'index'])->name('admin.budget-allocation');

    Route::get('/price-analysis', function () {
        return view('admin.price-analysis');
    })->name('admin.price-analysis');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
    
    Route::get('/admin/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('admin.transactions');
});

// Supplier Evaluation Routes
Route::get('/admin/suppliers/{supplier}/latest-evaluation', [SupplierRankingController::class, 'getLatestEvaluation'])
    ->name('admin.suppliers.latest-evaluation');
Route::get('/admin/suppliers/{supplier}/purchase-order-metrics', [SupplierRankingController::class, 'getPurchaseOrderMetrics'])
    ->name('admin.suppliers.purchase-order-metrics');

// Add this route for fetching contract items (materials) for web requests
Route::get('/contracts/{contract}/items', [\App\Http\Controllers\ContractController::class, 'getItems'])->name('contracts.items');

Route::resource('contracts', \App\Http\Controllers\ContractController::class);

// Search Routes
Route::prefix('search')->group(function () {
    Route::get('users', [SearchController::class, 'users'])->name('search.users');
    Route::get('contractors', [SearchController::class, 'contractors'])->name('search.contractors');
    Route::get('clients', [SearchController::class, 'clients'])->name('search.clients');
    Route::get('properties', [SearchController::class, 'properties'])->name('search.properties');
    Route::get('materials', [SearchController::class, 'materials'])->name('search.materials');
    Route::get('suppliers', [SearchController::class, 'suppliers'])->name('search.suppliers');
    Route::get('scope-types', [SearchController::class, 'scopeTypes'])->name('search.scope-types');
    Route::get('contracts', [SearchController::class, 'contracts'])->name('search.contracts');
});

// API Routes for Warranty Requests
Route::post('/api/warranty-requests', [WarrantyRequestController::class, 'store'])->middleware('auth');

// Budget Tracking Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/{contract}', [BudgetController::class, 'show'])->name('budgets.show');
    Route::get('/budgets/{contract}/export', [BudgetController::class, 'exportReport'])->name('budgets.export');
    Route::get('/budgets/alerts', [BudgetController::class, 'getBudgetAlerts'])->name('budgets.alerts');
});

// Supplier creation is now handled via sign up. Remove or redirect any suppliers.create routes.

// Procurement Routes
Route::middleware(['auth', 'role:procurement'])->prefix('procurement')->name('procurement.')->group(function () {
    Route::get('/dashboard', [ProcurementDashboardController::class, 'index'])->name('dashboard');
    
    // RFQ Routes
    Route::get('/rfqs', [ProcurementRfqController::class, 'index'])->name('rfqs.index');
    Route::get('/rfqs/create', [ProcurementRfqController::class, 'create'])->name('rfqs.create');
    Route::post('/rfqs', [ProcurementRfqController::class, 'store'])->name('rfqs.store');
    Route::get('/rfqs/{rfq}', [ProcurementRfqController::class, 'show'])->name('rfqs.show');
    Route::get('/rfqs/{rfq}/edit', [ProcurementRfqController::class, 'edit'])->name('rfqs.edit');
    Route::put('/rfqs/{rfq}', [ProcurementRfqController::class, 'update'])->name('rfqs.update');
    Route::delete('/rfqs/{rfq}', [ProcurementRfqController::class, 'destroy'])->name('rfqs.destroy');
    
    // Order Routes
    Route::get('/orders', [ProcurementOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [ProcurementOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [ProcurementOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [ProcurementOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [ProcurementOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [ProcurementOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [ProcurementOrderController::class, 'destroy'])->name('orders.destroy');
    
    // Supplier Routes
    Route::get('/suppliers', [ProcurementSupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [ProcurementSupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [ProcurementSupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [ProcurementSupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [ProcurementSupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [ProcurementSupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [ProcurementSupplierController::class, 'destroy'])->name('suppliers.destroy');
});

// Supplier Material Routes
Route::prefix('supplier')->name('supplier.')->middleware(['auth', 'verified', 'role:supplier'])->group(function () {
    Route::resource('materials', SupplierMaterialController::class);
    Route::get('dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
    Route::get('/ranking', [SupplierDashboardController::class, 'ranking'])->name('ranking');

    // Supplier Quotation Routes
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [SupplierQuotationController::class, 'index'])->name('index');
        Route::get('/{quotation}', [SupplierQuotationController::class, 'show'])->name('show');
        Route::post('/{quotation}/respond', [SupplierQuotationController::class, 'respond'])->name('respond');
    });
});
