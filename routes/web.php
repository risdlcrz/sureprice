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
});

// Admin protected routes
Route::middleware([AdminMiddleware::class])->group(function () {
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

    Route::get('/project-dashboard', function () {
        return view('admin.project-dashboard');
    })->name('admin.project');

    Route::get('/history-dashboard', function () {
        return view('admin.history-dashboard');
    })->name('admin.history');

    Route::get('/analytics-dashboard', function () {
        return view('admin.analytics-dashboard');
    })->name('admin.analytics');

    Route::get('/inventory', function () {
        return view('admin.inventory');
    })->name('admin.inventory');
});

// ================== Email Verification Routes ==================
// **Removed duplicate route /email/verify here**

// Contract Routes
Route::resource('contracts', ContractController::class);

// Material and Supplier Routes
Route::get('/materials/search', [MaterialController::class, 'search'])->name('materials.search');
Route::get('/materials/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('materials.suppliers');

// Client Search Route
Route::get('/clients/search', [PartyController::class, 'search'])->name('clients.search');

// Contract routes
Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');

// API routes for contract form
Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
Route::get('/materials/search', [MaterialController::class, 'search'])->name('materials.search');
Route::get('/materials/{material}/suppliers', [MaterialController::class, 'suppliers'])->name('materials.suppliers');
