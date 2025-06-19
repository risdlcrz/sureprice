<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Notifications\CompanyRejectedNotification;
use App\Notifications\CompanyApprovedNotification;

class AdminController extends Controller
{
// app/Http/Controllers/AdminController.php
public function dashboard()
{
    return view('admin.dbadmin'); // Make sure this view exists
}

public function pending()
{
    $companies = Company::with(['user', 'documents'])
        ->where('status', 'pending')
        ->latest()
        ->paginate(10);

    return view('admin.companies.pending', compact('companies'));
}

public function approve(Company $company)
{
    $company->update(['status' => 'approved']);
    
    // Send approval notification to the company user
    $company->user->notify(new CompanyApprovedNotification());
    
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
}


