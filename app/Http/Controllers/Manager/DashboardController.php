<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $globalUnreadCount = $unreadCount;
        return view('manager.notification-center', compact('notifications', 'unreadCount', 'globalUnreadCount'));
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
        $quotationRequest = \App\Models\QuotationRequest::with([
            'rooms.scopes.scopeType.materials'
        ])->findOrFail($id);

        // Build $selectedSuppliers: [material_id => supplier_id]
        $selectedSuppliers = $quotationRequest->selected_suppliers ?? [];
        $supplierIds = array_values($selectedSuppliers);
        $suppliers = \App\Models\Supplier::whereIn('id', $supplierIds)->get()->keyBy('id');
        // Fetch all quotations (RFQs) related to this quotation request
        $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->get();

        return view('manager.quotation-requests.show', compact('quotationRequest', 'selectedSuppliers', 'suppliers', 'rfqs'));
    }

    public function sendQuotationRequestToSuppliers($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);
        if (in_array($quotationRequest->status, ['reviewed', 'proceeded'])) {
            return redirect()->back()->with('error', 'This request has already been sent to suppliers.');
        }

        // Gather all material IDs from the request
        $materialIds = collect();
        foreach ($quotationRequest->rooms as $room) {
            foreach ($room->scopes as $scope) {
                if ($scope->scopeType && $scope->scopeType->materials) {
                    $materialIds = $materialIds->merge($scope->scopeType->materials->pluck('id'));
                }
            }
        }
        $materialIds = $materialIds->unique()->values();

        // Find all suppliers who can provide any of these materials
        $suppliers = \App\Models\Supplier::whereHas('materials', function($q) use ($materialIds) {
            $q->whereIn('materials.id', $materialIds);
        })->get();

        \DB::transaction(function () use ($suppliers, $materialIds, $quotationRequest) {
            foreach ($suppliers as $supplier) {
                $supplierMaterialIds = $supplier->materials()->whereIn('materials.id', $materialIds)->pluck('materials.id');
                if ($supplierMaterialIds->isEmpty()) continue;

                // Generate RFQ number with unique constraint check
                do {
                    $lastQuotation = \App\Models\Quotation::orderByDesc('id')->first();
                    if ($lastQuotation && preg_match('/RFQ-(\\d+)/i', $lastQuotation->rfq_number, $matches)) {
                        $nextNumber = intval($matches[1]) + 1;
                    } else {
                        $nextNumber = 1;
                    }
                    $rfqNumber = 'RFQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                } while (\App\Models\Quotation::where('rfq_number', $rfqNumber)->exists());

                $quotation = \App\Models\Quotation::create([
                    'purchase_request_id' => null,
                    'rfq_number' => $rfqNumber,
                    'status' => 'draft',
                    'notes' => 'Auto-generated from client quotation request #' . $quotationRequest->request_number,
                    'due_date' => now()->addDays(7),
                ]);
                // Attach supplier
                $quotation->suppliers()->attach($supplier->id);
                // Attach materials
                $materialSyncData = [];
                foreach ($supplierMaterialIds as $matId) {
                    $materialSyncData[$matId] = ['quantity' => 1];
                }
                $quotation->materials()->sync($materialSyncData);
                // Notify supplier's user
                if ($supplier->user) {
                    \App\Models\Notification::create([
                        'user_id' => $supplier->user->id,
                        'type' => 'rfq_created',
                        'notifiable_type' => \App\Models\Quotation::class,
                        'notifiable_id' => $quotation->id,
                        'data' => [
                            'title' => 'New RFQ Created',
                            'message' => 'A new Request for Quotation (RFQ #' . $quotation->rfq_number . ') has been created for you.',
                            'link' => route('supplier.quotations.show', $quotation->id),
                        ],
                        'for_role' => 'supplier',
                    ]);
                }
            }
            $quotationRequest->status = 'reviewed';
            $quotationRequest->save();
        });

        return redirect()->back()->with('success', 'RFQs have been created for all relevant suppliers.');
    }

    public function markAllNotificationsAsRead()
    {
        $user = auth()->user();
        \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearReadNotifications()
    {
        $user = auth()->user();
        \App\Models\Notification::where('user_id', $user->id)
            ->whereNotNull('read_at')
            ->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Read notifications cleared.');
    }

    public function markNotificationAsRead($id)
    {
        $user = auth()->user();
        $notification = \App\Models\Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if ($notification && is_null($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();
        }
        return response()->json(['success' => true]);
    }
} 