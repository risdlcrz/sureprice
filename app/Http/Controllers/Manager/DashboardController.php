<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $projects = \App\Models\Project::where('project_manager_id', $user->id)->paginate(10);
        return view('manager.dashboard', compact('projects'));
    }

    public function notificationCenter()
    {
        $user = auth()->user();
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
        return view('manager.notification-center', compact('notifications', 'unreadCount'));
    }

    public function quotationsPage()
    {
        $quotationRequests = \App\Models\QuotationRequest::with('user')->latest()->get();
        // Only show quotations that have been sent to suppliers
        $supplierQuotations = \App\Models\Quotation::with(['suppliers', 'materials', 'purchaseRequest'])
            ->where('status', 'sent')
            ->latest()
            ->get();
        $activeTab = request('tab', 'client');
        return view('manager.quotation-requests.index', compact('quotationRequests', 'supplierQuotations', 'activeTab'));
    }

    public function showClientQuotationRequest($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['user', 'rooms.scopes.scopeType.materials'])->findOrFail($id);
        return view('manager.quotation-requests.show', compact('quotationRequest'));
    }

    public function sendQuotationRequestToSuppliers($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);
        if ($quotationRequest->status === 'sent_to_suppliers') {
            return redirect()->back()->with('error', 'This request has already been sent to suppliers.');
        }
        $suppliers = \App\Models\Supplier::all();
        foreach ($suppliers as $supplier) {
            \App\Models\Notification::create([
                'user_id' => $supplier->user_id,
                'type' => 'rfq_sent',
                'notifiable_type' => \App\Models\QuotationRequest::class,
                'notifiable_id' => $quotationRequest->id,
                'data' => [
                    'title' => 'New RFQ Available',
                    'message' => 'A new RFQ (Request #' . $quotationRequest->request_number . ') is available for your review.',
                    'link' => route('supplier.quotations.index'),
                ],
                'for_role' => 'supplier',
            ]);
        }
        $quotationRequest->status = 'sent_to_suppliers';
        $quotationRequest->save();
        return redirect()->back()->with('success', 'Quotation request sent to all suppliers.');
    }
} 