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
use App\Http\Controllers\Supplier\SupplierPerformanceController;
use App\Http\Controllers\Warehouse\WarehouseDashboardController;
use App\Http\Controllers\Warehouse\WarehouseInventoryController;
use App\Http\Controllers\Warehouse\WarehouseDeliveryController;
use App\Http\Controllers\Warehouse\WarehouseReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PurchaseOrderPaymentController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\Warehouse\MaterialRequestApprovalController;
use App\Http\Controllers\ProjectTaskController;
// Home route redirect to login
Route::get('/', function () {
    return view('landing.catalogue');
})->name('landing.catalogue');
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
Route::get('/pending-approval', [RegisteredUserController::class, 'pendingApproval'])->name('pending.approval');

// ================== Legal Pages ==================
Route::get('/terms-conditions', function () {
    return view('legal.terms-conditions');
})->name('terms.conditions');

Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('privacy.policy');

// Auth required routes
Route::middleware(['auth'])->group(function () {
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
        Route::post('/', [ContractController::class, 'store'])->name('store');
        Route::get('/{contract}/edit', [ContractController::class, 'edit'])->name('edit');
        Route::put('/{contract}', [ContractController::class, 'update'])->name('update');
        Route::get('/{contract}', [ContractController::class, 'show'])->name('show');
        Route::get('/{contract}/download', [ContractController::class, 'download'])->name('download');
        Route::patch('/{contract}/status', [ContractController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{contract}/status', [ContractController::class, 'updateStatus']);
        Route::post('/{contract}/signatures', [ContractController::class, 'updateSignatures'])->name('updateSignatures');
        Route::post('/save-signature', [ContractController::class, 'saveSignature'])->name('contracts.save.signature');
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
    Route::middleware([\App\Http\Middleware\WarehouseMiddleware::class])->prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/dashboard', [WarehouseDashboardController::class, 'index'])->name('dashboard');
        Route::get('/stock-alerts', [WarehouseDashboardController::class, 'getStockAlerts'])->name('stock-alerts');
        Route::get('/stock-movements', [WarehouseDashboardController::class, 'getStockMovements'])->name('stock-movements');
        Route::resource('deliveries', WarehouseDeliveryController::class);
        Route::resource('inventory', WarehouseInventoryController::class);
        Route::post('inventory/add-stock', [WarehouseInventoryController::class, 'addStock'])->name('inventory.add-stock');
        Route::post('inventory/update-stock', [WarehouseInventoryController::class, 'updateStock'])->name('inventory.update-stock');
        Route::get('inventory/history/{material}', [WarehouseInventoryController::class, 'history'])->name('inventory.history');
        Route::get('reports/analytics', [WarehouseReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('reports/analytics/pdf', [WarehouseReportController::class, 'analyticsPdf'])->name('reports.analytics.pdf');
        Route::get('reports/inventory', [WarehouseReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('reports/movements', [WarehouseReportController::class, 'movements'])->name('reports.movements');
        Route::get('reports/deliveries', [WarehouseReportController::class, 'deliveries'])->name('reports.deliveries');
        Route::get('reports/usage', [WarehouseReportController::class, 'usage'])->name('reports.usage');
        Route::get('reports/inventory/pdf', [WarehouseReportController::class, 'inventoryPdf'])->name('reports.inventory.pdf');
        Route::get('reports/movements/pdf', [WarehouseReportController::class, 'movementsPdf'])->name('reports.movements.pdf');
        Route::get('reports/deliveries/pdf', [WarehouseReportController::class, 'deliveriesPdf'])->name('reports.deliveries.pdf');
        Route::get('reports/usage/pdf', [WarehouseReportController::class, 'usagePdf'])->name('reports.usage.pdf');
        Route::resource('reports', WarehouseReportController::class);
        Route::get('material-requests', [MaterialRequestApprovalController::class, 'index'])->name('material-requests.index');
        Route::post('material-requests/{materialRequest}/approve', [MaterialRequestApprovalController::class, 'approve'])->name('material-requests.approve');
        Route::get('material-requests/{materialRequest}', [MaterialRequestApprovalController::class, 'show'])->name('material-requests.show');
    });
    // Message routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/start', [MessageController::class, 'startConversation'])->name('messages.start');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{conversation}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::delete('/messages/message/{message}', [MessageController::class, 'destroyMessage'])->name('messages.message.destroy');
    Route::delete('/messages/attachment/{message}', [MessageController::class, 'removeAttachment'])->name('messages.attachment.remove');
    Route::get('/messages/attachment/{message}/download', [MessageController::class, 'downloadAttachment'])->name('messages.attachment.download');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/conversations/update', [MessageController::class, 'getConversationsUpdate'])->name('messages.conversations.update');
    // Make company search for chat available to all authenticated users (admin check in controller)
    Route::get('/admin/companies/search-for-chat', [\App\Http\Controllers\CompanyController::class, 'searchForChat'])->name('admin.companies.search-for-chat');
    // Purchase Order Payment Routes
    Route::post('/purchase-orders/{po}/payments', [PurchaseOrderPaymentController::class, 'store'])->name('purchase-orders.payments.store');
    Route::post('/purchase-order-payments/{payment}/verify', [PurchaseOrderPaymentController::class, 'verify'])->name('purchase-order-payments.verify');
    Route::post('/clients/{id}/ban', [\App\Http\Controllers\PartyController::class, 'ban'])->name('clients.ban');
    Route::post('/clients/{id}/unban', [\App\Http\Controllers\PartyController::class, 'unban'])->name('clients.unban');
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
    
    // Client Quotation Routes
    Route::prefix('quotation')->name('quotation.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\ClientQuotationController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\ClientQuotationController::class, 'store'])->name('store');
        Route::get('/suppliers', [\App\Http\Controllers\ClientQuotationController::class, 'suppliers'])->name('suppliers');
        Route::get('/recommend-suppliers', [\App\Http\Controllers\ClientQuotationController::class, 'recommendSuppliers'])->name('recommend-suppliers');
        Route::post('/submit', [\App\Http\Controllers\ClientQuotationController::class, 'submit'])->name('submit');
        Route::post('/save-supplier-selection', [\App\Http\Controllers\ClientQuotationController::class, 'saveSupplierSelection'])->name('saveSupplierSelection');
    });
    Route::get('/quotation', [\App\Http\Controllers\ClientQuotationController::class, 'index'])->name('quotation.index');
    Route::get('/quotation/create', [\App\Http\Controllers\ClientQuotationController::class, 'create'])->name('quotation.create');
    Route::post('/quotation', [\App\Http\Controllers\ClientQuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/view', [\App\Http\Controllers\ClientQuotationController::class, 'view'])->name('quotation.view');
    Route::post('/quotation/{id}/finalize', [\App\Http\Controllers\ClientQuotationController::class, 'finalizeSelection'])->name('quotation.finalize');
});
Route::post('/client/quotation/{id}/cancel', [App\Http\Controllers\ClientQuotationController::class, 'cancel'])->name('client.quotation.cancel');
Route::get('/client/quotation/{id}/contract', [App\Http\Controllers\ClientQuotationController::class, 'showContractForm'])->name('client.contract.fill');
Route::post('/client/quotation/{id}/proceed', [\App\Http\Controllers\ClientQuotationController::class, 'finalizeSelection'])->name('client.quotation.proceed');

// Procurement Routes
Route::middleware(['auth', \App\Http\Middleware\ProcurementMiddleware::class])->prefix('procurement')->name('procurement.')->group(function () {
    Route::get('/dashboard', [ProcurementController::class, 'index'])->name('dashboard');
    
    // Project Management Routes
    Route::get('/projects', [ProcurementController::class, 'projectDashboard'])->name('projects.index');
    Route::get('/projects/{project}', [ProcurementController::class, 'projectShow'])->name('projects.show');
    Route::get('/projects/{project}/tasks', [ProcurementController::class, 'projectTasks'])->name('projects.tasks');
    Route::get('/projects/{project}/procurement', [ProcurementController::class, 'projectProcurement'])->name('projects.procurement');
    Route::get('/projects/{project}/analytics', [ProcurementController::class, 'projectAnalytics'])->name('projects.analytics');
    
    // Inventory Routes
    Route::get('/inventory', [ProcurementController::class, 'inventoryDashboard'])->name('inventory.index');
    Route::get('/inventory/create', [ProcurementController::class, 'inventoryCreate'])->name('inventory.create');
    Route::post('/inventory', [ProcurementController::class, 'inventoryStore'])->name('inventory.store');
    Route::get('/inventory/{inventory}/edit', [ProcurementController::class, 'inventoryEdit'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [ProcurementController::class, 'inventoryUpdate'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [ProcurementController::class, 'inventoryDestroy'])->name('inventory.destroy');
    Route::post('/inventory/{inventory}/adjust-stock', [ProcurementController::class, 'inventoryAdjustStock'])->name('inventory.adjust-stock');
    Route::get('/inventory/low-stock', [ProcurementController::class, 'inventoryLowStock'])->name('inventory.low-stock');
    Route::get('/inventory/expiring', [ProcurementController::class, 'inventoryExpiring'])->name('inventory.expiring');
    
    // Analytics Routes
    Route::get('/analytics', [ProcurementController::class, 'analyticsDashboard'])->name('analytics');
    Route::get('/analytics/transactions', [AnalyticsController::class, 'transactions'])->name('analytics.transactions');
    Route::get('/analytics/budget-allocation', [AnalyticsController::class, 'budgetAllocation'])->name('analytics.budget-allocation');
    Route::get('/analytics/price-analysis', [AnalyticsController::class, 'priceAnalysis'])->name('analytics.price-analysis');
    
    // History and Notifications
    Route::get('/history', [ProcurementController::class, 'projectHistory'])->name('history');
    Route::get('/notifications', [ProcurementController::class, 'notificationHub'])->name('notification');
});
// ================== Email Verification Routes ==================
// **Removed duplicate route /email/verify here**
// Remove duplicate client search route
// Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
// Admin protected routes
// Manager protected operational routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('information-management', InformationManagementController::class);
    Route::post('information-management/import', [InformationManagementController::class, 'import'])->name('information-management.import');
    Route::get('information-management/template/download', [InformationManagementController::class, 'template'])->name('information-management.template');
    Route::get('/analytics-dashboard', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/supplier-rankings', [AnalyticsController::class, 'supplierRankings'])->name('admin.supplier-rankings');
    Route::get('/supplier-rankings/top', [AnalyticsController::class, 'getTopSuppliers'])->name('admin.supplier-rankings.top');
    Route::get('/purchase-order', function () {
        return view('admin.purchase-order');
    })->name('admin.purchase-order');
    Route::get('/budget-allocation', [BudgetAllocationController::class, 'index'])->name('admin.budget-allocation');
    Route::get('/price-analysis', [App\Http\Controllers\Admin\MaterialController::class, 'priceAnalysis'])->name('admin.price-analysis');
    Route::get('/admin/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('admin.transactions');
    Route::get('/admin/quotation-requests/{id}/review', [App\Http\Controllers\AdminController::class, 'review'])->name('admin.quotation.review');
    Route::post('/admin/quotation-requests/{id}/send-rfq', [App\Http\Controllers\AdminController::class, 'sendRfqToSuppliers'])->name('admin.quotation.send-rfq');
    Route::post('/admin/quotation-requests/{id}/finalize', [App\Http\Controllers\AdminController::class, 'finalizeQuotationSelection'])->name('admin.quotation.finalize');
    Route::get('/admin/quotation-requests/{id}/recommend-suppliers', [App\Http\Controllers\AdminController::class, 'recommendSuppliers'])->name('admin.quotation.recommend-suppliers');
    Route::get('/admin/quotation-requests/{id}/json', [App\Http\Controllers\AdminController::class, 'quotationRequestJson'])->name('admin.quotation-request.json');
});
// Admin approval/oversight routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/companies/pending', [AdminController::class, 'pending'])->name('admin.companies.pending');
    Route::post('/admin/companies/{company}/approve', [AdminController::class, 'approve'])->name('admin.companies.approve');
    Route::post('/admin/companies/{company}/reject', [AdminController::class, 'reject'])->name('admin.companies.reject');
    Route::get('/admin/companies/{company}', [AdminController::class, 'show'])->name('admin.companies.show');
    Route::patch('/admin/companies/{company}/status', [App\Http\Controllers\AdminController::class, 'updateStatus'])->name('admin.companies.update');
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
    Route::get('/notification-center', [\App\Http\Controllers\AdminController::class, 'notificationCenter'])->name('admin.notification');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\AdminController::class, 'markNotificationAsRead'])->name('admin.notifications.mark-read');
    Route::get('/admin/logs', [App\Http\Controllers\AdminController::class, 'administratorLogs'])->name('admin.logs');
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
    Route::get('quotation-requests', [SearchController::class, 'quotationRequests'])->name('search.quotation-requests'); // <-- Added
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
// Supplier Material Routes
Route::prefix('supplier')->name('supplier.')->middleware(['auth', 'verified', \App\Http\Middleware\SupplierMiddleware::class])->group(function () {
    Route::get('dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');

    Route::get('profile/edit', [SupplierDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('profile/update', [SupplierDashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('materials/search', [SupplierMaterialController::class, 'search'])->name('materials.search');
    Route::post('materials/link', [SupplierMaterialController::class, 'link'])->name('materials.link');
    Route::resource('materials', SupplierMaterialController::class);

    Route::get('quotations', [SupplierQuotationController::class, 'index'])->name('quotations.index');
    Route::get('quotations/{quotation}', [SupplierQuotationController::class, 'show'])->name('quotations.show');
    Route::post('quotations/{quotation}/respond', [SupplierQuotationController::class, 'respond'])->name('quotations.respond');
    Route::post('quotations/discount-info', [SupplierQuotationController::class, 'getDiscountInfo'])->name('quotations.discount-info');

    Route::get('ranking', [SupplierDashboardController::class, 'ranking'])->name('ranking');
    Route::get('performance', [SupplierPerformanceController::class, 'index'])->name('performance');

    // Purchase Requests for Supplier
    Route::get('purchase-requests', [\App\Http\Controllers\Supplier\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
    Route::get('purchase-requests/{purchaseRequest}', [\App\Http\Controllers\Supplier\PurchaseRequestController::class, 'show'])->name('purchase-requests.show');
    Route::post('purchase-requests/{purchaseRequest}/approve', [\App\Http\Controllers\PurchaseRequestController::class, 'supplierApprove'])->name('purchase-requests.approve');

    // Purchase Orders for Supplier
    Route::get('purchase-orders', [\App\Http\Controllers\Supplier\PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/{purchaseOrder}', [\App\Http\Controllers\Supplier\PurchaseOrderController::class, 'show'])->name('purchase-orders.show');

    Route::get('notification-center', [\App\Http\Controllers\Supplier\SupplierDashboardController::class, 'notificationCenter'])->name('notification');
});
// Admin Supplier Profile Update Review Routes
Route::middleware(['auth', 'admin'])->prefix('admin/suppliers')->name('admin.suppliers.')->group(function () {
    Route::get('pending-updates', [\App\Http\Controllers\SupplierController::class, 'pendingUpdates'])->name('pending-updates');
    Route::get('review-update/{id}', [\App\Http\Controllers\SupplierController::class, 'reviewUpdate'])->name('review-update');
    Route::post('approve-update/{id}', [\App\Http\Controllers\SupplierController::class, 'approveUpdate'])->name('approve-update');
    Route::post('reject-update/{id}', [\App\Http\Controllers\SupplierController::class, 'rejectUpdate'])->name('reject-update');
});

// Admin Material Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('materials', AdminMaterialController::class);
    Route::post('materials/{material}/suppliers', [AdminMaterialController::class, 'updateSuppliers'])->name('materials.suppliers.update');
});

Route::resource('material-requests', \App\Http\Controllers\MaterialRequestController::class);
Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);

Route::post('purchase-requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');
Route::post('purchase-requests/{purchaseRequest}/supplier-approve', [PurchaseRequestController::class, 'supplierApprove'])->name('purchase-requests.supplier.approve')->middleware('auth', 'role:supplier');

// Material Search
Route::get('/materials/search', [MaterialController::class, 'search'])->name('materials.search');
Route::get('/materials/all', [MaterialController::class, 'getAllMaterials'])->name('materials.all');

// Project Management Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::get('projects/{project}/feedback', [ProjectController::class, 'feedbackForm'])->name('projects.feedback');
    Route::post('projects/{project}/feedback', [ProjectController::class, 'submitFeedback'])->name('projects.submitFeedback');
    Route::post('projects/{project}/progress', [ProjectController::class, 'updateProgress'])->name('projects.progress.update');
    Route::resource('projects.tasks', ProjectTaskController::class);
});

Route::get('/api/clients/{id}/ban-status', [\App\Http\Controllers\PartyController::class, 'banStatus'])->name('api.clients.ban-status');

Route::post('purchase-orders/{id}/ship', [\App\Http\Controllers\PurchaseOrderController::class, 'markAsShipped'])->name('purchase-orders.ship');
Route::post('purchase-orders/{id}/deliver', [\App\Http\Controllers\PurchaseOrderController::class, 'markAsDelivered'])->name('purchase-orders.deliver');

// Supplier Recommendation Route
Route::get('/projects/{projectId}/recommend-suppliers', [ProjectController::class, 'recommendSuppliers'])->name('projects.recommend-suppliers');

// General Supplier Recommendation Route for Analytics
Route::get('/analytics/supplier-recommendation', [ProjectController::class, 'generalSupplierRecommendation'])->name('analytics.supplier-recommendation');

// AJAX route for supplier recommendation in material requests
Route::get('/material-requests/recommend-suppliers-for-material', [\App\Http\Controllers\MaterialRequestController::class, 'recommendSuppliersForMaterial']);

// AJAX route for supplier recommendation in purchase requests
Route::get('/purchase-requests/recommend-suppliers-for-material', [\App\Http\Controllers\PurchaseRequestController::class, 'recommendSuppliersForMaterial']);

// Procurement Supplier Rankings
Route::middleware(['auth', \App\Http\Middleware\ProcurementMiddleware::class])->prefix('procurement')->name('procurement.')->group(function () {
    Route::get('suppliers/rankings', [App\Http\Controllers\ProcurementController::class, 'suppliersRankings'])->name('suppliers.rankings');
});

// General Supplier Recommendation Route for Procurement Analytics
Route::get('/procurement/analytics/supplier-recommendation', [\App\Http\Controllers\ProcurementController::class, 'generalSupplierRecommendation'])->name('procurement.analytics.supplier-recommendation');

Route::get('/procurement/logs', [App\Http\Controllers\ProcurementController::class, 'procurementLogs'])->name('procurement.logs');

Route::get('/admin/logs', [App\Http\Controllers\AdminController::class, 'administratorLogs'])->name('admin.logs');

Route::get('/warehouse/logs', [App\Http\Controllers\Warehouse\WarehouseDashboardController::class, 'warehouseLogs'])->name('warehouse.logs');

// Procurement request approval routes
Route::post('/purchase-requests/request-approval', [\App\Http\Controllers\PurchaseRequestController::class, 'requestApproval'])->name('purchase-requests.request-approval');
Route::post('/purchase-orders/request-approval', [\App\Http\Controllers\PurchaseOrderController::class, 'requestApproval'])->name('purchase-orders.request-approval');

Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::get('/finance/dashboard', [\App\Http\Controllers\FinanceDashboardController::class, 'index'])->name('finance.dashboard');
    Route::get('/finance/payments', [\App\Http\Controllers\FinanceDashboardController::class, 'payments'])->name('finance.payments');
    Route::get('/finance/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('finance.transactions');
    Route::post('/finance/payments/{purchaseOrder}/pay', [\App\Http\Controllers\FinanceDashboardController::class, 'pay'])->name('finance.pay');
});

// API endpoint for fetching a single quotation request by ID
Route::get('/api/quotation-requests/{id}', [App\Http\Controllers\QuotationRequestController::class, 'showJson']);

// Manager dashboard and routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notification-center', [\App\Http\Controllers\Manager\DashboardController::class, 'notificationCenter'])->name('notification');
    Route::get('/quotations', [\App\Http\Controllers\Manager\DashboardController::class, 'quotationsPage'])->name('quotations');
    Route::get('/quotation-requests/{id}/view', [\App\Http\Controllers\Manager\DashboardController::class, 'showClientQuotationRequest'])->name('quotation-requests.view');
    Route::post('/quotation-requests/{id}/send-to-suppliers', [\App\Http\Controllers\Manager\DashboardController::class, 'sendQuotationRequestToSuppliers'])->name('quotation-requests.send-to-suppliers');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Manager\DashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/clear-read', [\App\Http\Controllers\Manager\DashboardController::class, 'clearReadNotifications'])->name('notifications.clearRead');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Manager\DashboardController::class, 'markNotificationAsRead'])->name('notifications.markAsRead');
    // Add more manager routes here
});

// Admin dashboard (for oversight/approval)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Add more admin routes here
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dbadmin', function () {
        return view('admin.dbadmin');
    })->name('dbadmin');
});

// Test route for notification count (remove in production)
Route::get('/test-notifications', function () {
    $service = new \App\Services\NotificationService();
    $count = $service::getUnreadCount();
    return response()->json([
        'unread_count' => $count,
        'user_id' => auth()->id(),
        'user_name' => auth()->user()->name ?? 'Unknown'
    ]);
})->middleware('auth');

// Test route for creating a sample purchase request notification (remove in production)
Route::get('/test-purchase-request-notification', function () {
    if (!auth()->user()->hasRole('admin')) {
        return response()->json(['error' => 'Admin access required'], 403);
    }
    
    // Create a sample notification
    \App\Models\Notification::create([
        'notifiable_id' => auth()->id(),
        'notifiable_type' => \App\Models\User::class,
        'type' => 'Purchase Request Approval Needed',
        'data' => [
            'title' => 'Test Purchase Request Approval Required',
            'message' => 'A test purchase request #PR-000001 requires your approval.',
            'link' => route('purchase-requests.index'),
            'purchase_request_id' => 1,
            'request_number' => 'PR-000001'
        ],
    ]);
    
    return response()->json(['success' => true, 'message' => 'Test notification created']);
})->middleware('auth');

// Clean up invalid notifications (remove in production)
Route::get('/cleanup-notifications', function () {
    if (!auth()->user()->hasRole('admin')) {
        return response()->json(['error' => 'Admin access required'], 403);
    }
    
    // Delete notifications with invalid purchase request data
    $deleted = \App\Models\Notification::where('type', 'like', '%Purchase Request%')
        ->where(function($query) {
            $query->whereNull('data')
                  ->orWhere('data', '')
                  ->orWhere('data', '[]');
        })
        ->delete();
    
    return response()->json(['success' => true, 'deleted_count' => $deleted]);
})->middleware('auth');

Route::get('/api/unread-notifications-count', function () {
    $user = auth()->user();
    $count = \App\Models\Notification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count();
    return response()->json(['count' => $count]);
})->middleware('auth');

Route::middleware(['auth'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::resource('deliveries', \App\Http\Controllers\Warehouse\WarehouseDeliveryController::class);
    Route::get('deliveries/{delivery}/feedback', [\App\Http\Controllers\Warehouse\WarehouseDeliveryController::class, 'feedbackForm'])->name('deliveries.feedback');
    Route::post('deliveries/{delivery}/feedback', [\App\Http\Controllers\Warehouse\WarehouseDeliveryController::class, 'submitFeedback'])->name('deliveries.submitFeedback');
});

Route::post('/purchase-order-payments/{purchaseOrder}', [\App\Http\Controllers\PurchaseOrderPaymentController::class, 'store'])->name('purchase-order-payments.store');

// Admin notification center actions
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/notifications/mark-all-as-read', [App\Http\Controllers\AdminController::class, 'markAllNotificationsAsRead'])->name('admin.notifications.markAllAsRead');
    Route::post('/admin/notifications/clear-read', [App\Http\Controllers\AdminController::class, 'clearReadNotifications'])->name('admin.notifications.clearRead');
});
// Supplier notification center actions
Route::middleware(['auth', 'verified', App\Http\Middleware\SupplierMiddleware::class])->prefix('supplier')->name('supplier.')->group(function () {
    Route::post('notifications/mark-all-as-read', [App\Http\Controllers\Supplier\SupplierDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.markAllAsRead');
    Route::post('notifications/clear-read', [App\Http\Controllers\Supplier\SupplierDashboardController::class, 'clearReadNotifications'])->name('notifications.clearRead');
});
