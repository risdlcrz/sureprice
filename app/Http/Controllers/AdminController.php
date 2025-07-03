<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Notifications\CompanyRejectedNotification;
use App\Notifications\CompanyApprovedNotification;
use App\Models\Activity;

class AdminController extends Controller
{
// app/Http/Controllers/AdminController.php
private function logPageView($description, $modelType = null, $modelId = null)
{
    Activity::create([
        'user_id' => auth()->id(),
        'action' => 'viewed',
        'description' => $description,
        'model_type' => $modelType,
        'model_id' => $modelId
    ]);
}

public function dashboard()
{
    $this->logPageView('Viewed Admin Dashboard');
    return view('admin.dbadmin'); // Make sure this view exists
}

public function pending()
{
    $this->logPageView('Viewed Pending Companies');
    $companies = Company::with(['user', 'documents'])
        ->where('status', 'pending')
        ->latest()
        ->paginate(10);

    return view('admin.companies.pending', compact('companies'));
}

public function approve(Company $company)
{
    $company->update(['status' => 'approved']);

    if ($company->designation === 'supplier') {
        $address = trim(implode(', ', array_filter([
            $company->street,
            $company->barangay,
            $company->city,
            $company->state,
            $company->postal
        ])));
        
        \App\Models\Supplier::updateOrCreate(
            [
                'email' => $company->email,
            ],
            [
                'company_name' => $company->company_name,
                'contact_person' => $company->contact_person,
                'phone' => $company->mobile_number,
                'address' => $address,
                'status' => 'active',
                'registration_number' => $company->business_reg_no,
                'user_id' => $company->user_id,
                'company_id' => $company->id,
            ]
        );
    }
    
    // Send approval notification to the company user
    if ($company->user) {
        $company->user->notify(new CompanyApprovedNotification());
    }
    
    return back()->with('success', 'Company approved successfully!');
}

public function reject(Request $request, Company $company)
{
    $request->validate([
        'rejection_reason' => 'required|string|max:255'
    ]);
    
    $company->update([
        'status' => 'rejected',
        'rejection_reason' => $request->rejection_reason
    ]);
    
    // Send rejection notification to the company user
    $company->user->notify(new CompanyRejectedNotification($request->rejection_reason));
    
    return back()->with('success', 'Company has been rejected.');
}

public function show(Company $company)
{
    // Load relationships and ensure admin can view regardless of status
    $company->load(['user', 'documents', 'bankDetails']);
    
    // If company doesn't exist, redirect to companies list
    if (!$company) {
        return redirect()->route('information-management.index', ['type' => 'company'])
                        ->with('error', 'Company not found.');
    }
    
    return view('admin.companies.show', compact('company'));
}

public function notificationCenter()
{
    // Fetch the latest 50 activities for the notification hub
    $activities = \App\Models\Activity::latest()->take(50)->get();
    return view('admin.notification', compact('activities'));
}

public function updateStatus(Request $request, Company $company)
{
    $request->validate([
        'status' => 'required|in:approved,pending,rejected',
    ]);
    $oldStatus = $company->status;
    $company->status = $request->status;
    if ($request->status === 'rejected') {
        $company->rejection_reason = $request->input('rejection_reason', 'Manually set to rejected by admin.');
        $company->user->notify(new CompanyRejectedNotification($company->rejection_reason));
    } elseif ($request->status === 'approved' && $oldStatus !== 'approved') {
        $company->rejection_reason = null;
        $company->user->notify(new CompanyApprovedNotification());
    } else {
        $company->rejection_reason = null;
    }
    $company->save();
    return back()->with('success', 'Company status updated to ' . $request->status . '.');
}

public function informationManagement()
{
    $this->logPageView('Viewed Information Management');
    // ... existing code ...
}

public function historyDashboard()
{
    $this->logPageView('Viewed History Dashboard');
    // ... existing code ...
}

public function administratorLogs(Request $request)
{
    $this->logPageView('Viewed Administrator Logs');
    $user = auth()->user();
    $filter = $request->get('filter', 'all');

    $activities = \App\Models\Activity::with('user')
        ->when($user->user_type === 'employee' && $user->role === 'procurement', function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('user_type', 'employee')->where('role', 'procurement');
            });
        })
        ->when($user->user_type === 'employee' && $user->role === 'warehousing', function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('user_type', 'employee')->where('role', 'warehousing');
            });
        })
        ->when($user->user_type === 'admin' && in_array($filter, ['admin', 'procurement', 'warehousing']), function ($query) use ($filter) {
            if ($filter === 'admin') {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'admin');
                });
            } elseif ($filter === 'procurement') {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'employee')->where('role', 'procurement');
                });
            } elseif ($filter === 'warehousing') {
                $query->whereHas('user', function ($q) {
                    $q->where('user_type', 'employee')->where('role', 'warehousing');
                });
            }
        })
        ->latest()
        ->take(50)
        ->get();

    $userTypes = [
        'all' => 'All',
        'admin' => 'Admin',
        'procurement' => 'Procurement',
        'warehousing' => 'Warehousing',
    ];

    return view('admin.logs', compact('activities', 'userTypes', 'filter'));
}
}