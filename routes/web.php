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
    Route::resource('contracts', ContractController::class);
    Route::get('contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');
    Route::patch('contracts/{contract}/status', [ContractController::class, 'updateStatus'])->name('contracts.updateStatus');
    Route::get('/project-timeline', [ContractController::class, 'projectTimeline'])->name('project.timeline');
    
    // Supporting routes for contract form
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/materials/search', [MaterialController::class, 'search'])->name('materials.search');
    Route::get('/materials/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('materials.suppliers');

    // Material Routes
    Route::resource('materials', MaterialController::class);
    Route::get('/api/materials/search', [MaterialController::class, 'apiSearch'])->name('api.materials.search');

    // Inquiry Routes
    Route::resource('inquiries', InquiryController::class);
    Route::post('/api/inquiries/{inquiry}/remove-attachment', [InquiryController::class, 'removeAttachment']);
    Route::get('/api/inquiries/search', [InquiryController::class, 'search'])->name('inquiries.search');

    // Quotation Routes
    Route::resource('quotations', QuotationController::class);
    Route::post('/api/quotations/{quotation}/send', [QuotationController::class, 'send']);
    Route::post('/api/quotations/{quotation}/approve', [QuotationController::class, 'approve']);
    Route::post('/api/quotations/{quotation}/reject', [QuotationController::class, 'reject']);
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

    // Purchase Requests
    Route::resource('purchase-requests', PurchaseRequestController::class);
    Route::post('purchase-requests/{purchaseRequest}/status', [PurchaseRequestController::class, 'updateStatus'])->name('purchase-requests.update-status');
    Route::get('/api/purchase-requests/{purchaseRequest}/items', [PurchaseRequestController::class, 'getItems'])->name('api.purchase-requests.items');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::get('purchase-orders/{id}/json', [App\Http\Controllers\PurchaseOrderController::class, 'showJson'])->name('purchase-orders.json');

    // Transaction Routes
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

    // Other admin routes
    Route::get('/notification-center', function () {
        return view('admin.notification-center');
    })->name('admin.notification');

    Route::get('/history-dashboard', function () {
        return view('admin.history-dashboard');
    })->name('admin.history');

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

    Route::get('/inventory', function () {
        return view('admin.inventory');
    })->name('admin.inventory');
    
    Route::get('/admin/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('admin.transactions');
});

// Password Change Routes
Route::get('/change-password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'showChangeForm'])
    ->name('change.password.form');
Route::post('/change-password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])
    ->name('change.password.update');

// Supplier Evaluation Routes
Route::get('/admin/suppliers/{supplier}/latest-evaluation', [SupplierRankingController::class, 'getLatestEvaluation'])
    ->name('admin.suppliers.latest-evaluation');
