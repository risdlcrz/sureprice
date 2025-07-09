<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Notifications\CompanyRejectedNotification;
use App\Notifications\CompanyApprovedNotification;
use App\Models\Activity;
use App\Models\QuotationRequest;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Services\SupplierSelectionService;

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
    // Fetch notifications for the current admin user using both old (user_id) and new (notifiable_id) methods
    $notifications = \App\Models\Notification::where(function($query) {
            $query->where('notifiable_id', auth()->id())
                  ->orWhere('user_id', auth()->id());
        })
        ->latest()
        ->take(50)
        ->get();
    return view('admin.notification', compact('notifications'));
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

public function review($id)
{
    $quotationRequest = QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);
    $this->logPageView('Reviewed Client Quotation Request #' . $quotationRequest->request_number, QuotationRequest::class, $quotationRequest->id);

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

    // Check if any RFQs have already been created for this QuotationRequest
    $rfqsSent = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->exists();

    // Build materialSupplierResponses for the view
    $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')
        ->with(['responses.items', 'responses.supplier'])
        ->get();

    $materialSupplierResponses = [];
    foreach ($materialIds as $materialId) {
        $offers = [];
        foreach ($rfqs as $rfq) {
            foreach ($rfq->responses as $response) {
                foreach ($response->items as $item) {
                    if ($item->material_id == $materialId) {
                        $offers[] = [
                            'supplier_name' => $response->supplier->company_name ?? 'Unknown',
                            'unit_price' => $item->unit_price,
                        ];
                    }
                }
            }
        }
        $materialSupplierResponses[$materialId] = $offers;
    }

    return view('admin.quotation-requests.review', compact('quotationRequest', 'rfqsSent', 'materialSupplierResponses'));
}

// Helper to get badges for a supplier's response for a material
private function getSupplierBadges($supplier, $unitPrice, $materialId, $rfqs)
{
    // Gather all prices for this material from all suppliers
    $prices = [];
    $deliveryRates = [];
    $defectRates = [];
    foreach ($rfqs as $rfq) {
        foreach ($rfq->responses as $response) {
            foreach ($response->items as $item) {
                if ($item->material_id == $materialId) {
                    $prices[$response->supplier_id] = $item->unit_price;
                    $deliveryRates[$response->supplier_id] = $response->supplier->metrics->on_time_delivery_rate ?? 0;
                    $defectRates[$response->supplier_id] = $response->supplier->metrics->average_defect_rate ?? 0;
                }
            }
        }
    }
    $badges = [];
    if (!empty($prices)) {
        $minPrice = min($prices);
        $maxDelivery = max($deliveryRates);
        $minDefect = min($defectRates);
        if ($unitPrice == $minPrice) $badges[] = 'Cheapest';
        if (($supplier->metrics->on_time_delivery_rate ?? 0) == $maxDelivery) $badges[] = 'Best Delivery';
        if (($supplier->metrics->average_defect_rate ?? 0) == $minDefect) $badges[] = 'Least Defects';
        // You can add more logic for "Overall Best" using your KNN/LP service
    }
    return $badges;
}

public function sendRfqToSuppliers($id)
{
    $quotationRequest = QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);

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
    $suppliers = Supplier::whereHas('materials', function($q) use ($materialIds) {
        $q->whereIn('materials.id', $materialIds);
    })->get();

    // For each supplier, create a Quotation (RFQ) with all their materials from the request
    foreach ($suppliers as $supplier) {
        $supplierMaterialIds = $supplier->materials()->whereIn('materials.id', $materialIds)->pluck('materials.id');
        if ($supplierMaterialIds->isEmpty()) continue;

        // Generate RFQ number
        $lastQuotation = Quotation::orderByDesc('id')->first();
        if ($lastQuotation && preg_match('/RFQ-(\\d+)/i', $lastQuotation->rfq_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $rfqNumber = 'RFQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $quotation = Quotation::create([
            'purchase_request_id' => null, // Not linked to a PR
            'rfq_number' => $rfqNumber,
            'status' => 'draft',
            'notes' => 'Auto-generated from client quotation request #' . $quotationRequest->request_number,
            'due_date' => Carbon::now()->addDays(7),
        ]);
        // Attach supplier
        $quotation->suppliers()->attach($supplier->company_id ?? $supplier->id);
        // Attach materials
        $materialSyncData = [];
        foreach ($supplierMaterialIds as $matId) {
            $materialSyncData[$matId] = ['quantity' => 1]; // Quantity can be improved if needed
        }
        $quotation->materials()->sync($materialSyncData);
        // Notify supplier's user
        \Log::info('Attempting to notify supplier', ['supplier_id' => $supplier->id, 'user_id' => $supplier->user_id, 'user_exists' => $supplier->user ? true : false]);
        if ($supplier->user) {
            $notification = \App\Models\Notification::create([
                'user_id' => $supplier->user->id,
                'type' => 'rfq_created',
                'notifiable_type' => Quotation::class,
                'notifiable_id' => $quotation->id,
                'data' => [
                    'title' => 'New RFQ Created',
                    'message' => 'A new Request for Quotation (RFQ #' . $quotation->rfq_number . ') has been created for you.',
                    'link' => route('supplier.quotations.show', $quotation->id),
                ],
                'for_role' => 'supplier',
            ]);
            \Log::info('Notification created', ['notification_id' => $notification->id, 'user_id' => $supplier->user->id]);
        } else {
            \Log::warning('Supplier has no user, notification not sent', ['supplier_id' => $supplier->id]);
        }
    }

    // Mark the QuotationRequest as reviewed
    $quotationRequest->status = 'reviewed';
    $quotationRequest->save();

    return redirect()->route('admin.quotation.review', ['id' => $quotationRequest->id])
        ->with('success', 'RFQs have been created for all relevant suppliers.');
}

public function finalizeQuotationSelection(Request $request, $id)
{
    $quotationRequest = QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);
    $selectedSuppliers = $request->input('selected_suppliers', []);

    // Find all RFQs (Quotations) generated for this QuotationRequest
    $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['materials'])->get();

    // For each material, update the selected_supplier_id in material_quotation
    foreach ($selectedSuppliers as $materialId => $supplierId) {
        foreach ($rfqs as $rfq) {
            // Update the pivot for this material in this RFQ
            $rfq->materials()->updateExistingPivot($materialId, ['selected_supplier_id' => $supplierId]);
        }
    }

    // Notify the client (user who created the quotation request)
    if ($quotationRequest->user_id) {
        \App\Models\Notification::create([
            'user_id' => $quotationRequest->user_id,
            'type' => 'quotation_finalized',
            'notifiable_type' => QuotationRequest::class,
            'notifiable_id' => $quotationRequest->id,
            'data' => [
                'title' => 'Quotation Finalized',
                'message' => 'Your quotation request has been finalized. You can now view supplier offers and selected suppliers.',
                'link' => route('client.quotation.view', $quotationRequest->id),
            ],
            'for_role' => 'client',
        ]);
    }

    return redirect()->route('admin.quotation.review', ['id' => $quotationRequest->id])
        ->with('success', 'Supplier selections saved and client notified.');
}

public function recommendSuppliers(Request $request, $id)
{
    $category = $request->query('category', 'overall_best');
    $quotationRequest = QuotationRequest::with(['rooms.scopes.scopeType.materials'])->findOrFail($id);

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

    // Fetch all RFQs (Quotations) generated for this QuotationRequest
    $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with(['suppliers', 'materials', 'responses.items', 'responses.supplier.metrics'])->get();

    // Build supplier data for the service
    $suppliers = [];
    foreach ($rfqs as $rfq) {
        foreach ($rfq->responses as $response) {
            $supplier = $response->supplier;
            $metrics = $supplier->metrics;
            $materialIdsForSupplier = [];
            foreach ($response->items as $item) {
                $materialIdsForSupplier[] = $item->material_id;
            }
            $suppliers[] = [
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'material_ids' => $materialIdsForSupplier,
                'price_map' => collect($response->items)->mapWithKeys(fn($item) => [$item->material_id => $item->unit_price])->toArray(),
                'on_time_delivery_rate' => $metrics->on_time_delivery_rate ?? 0,
                'average_defect_rate' => $metrics->average_defect_rate ?? 0,
                'average_cost_variance' => $metrics->average_cost_variance ?? 0,
            ];
        }
    }

    $service = new SupplierSelectionService();
    $materialSegments = $service->segmentSuppliersByMaterial($suppliers);

    $recommendations = [];
    foreach ($materialIds as $materialId) {
        $ranked = $service->getSuppliersForMaterial($materialSegments, $materialId, $category);
        if (!empty($ranked)) {
            $recommendations[$materialId] = $ranked[0]['id'];
        }
    }

    return response()->json(['recommendations' => $recommendations]);
}

    /**
     * Return a finalized quotation request and its related data as JSON for contract autofill.
     */
    public function quotationRequestJson($id)
    {
        $quotationRequest = \App\Models\QuotationRequest::with(['user', 'rooms.scopes.scopeType.materials'])->findOrFail($id);
        // Gather property info (if any)
        $property = [
            'address' => $quotationRequest->property->address ?? '',
            'street' => $quotationRequest->property->street ?? '',
            'city' => $quotationRequest->property->city ?? '',
            'state' => $quotationRequest->property->state ?? '',
            'postal' => $quotationRequest->property->postal ?? '',
        ];
        // Gather all items (materials, quantities, prices, units)
        $items = [];
        foreach ($quotationRequest->rooms as $room) {
            foreach ($room->scopes as $scope) {
                if ($scope->scopeType && $scope->scopeType->materials) {
                    foreach ($scope->scopeType->materials as $material) {
                        $items[] = [
                            'material' => [
                                'id' => $material->id,
                                'name' => $material->name,
                            ],
                            'quantity' => $material->pivot->quantity ?? 1,
                            'unit' => $material->unit ?? 'pcs',
                            'unit_price' => $material->pivot->unit_price ?? 0,
                            'amount' => ($material->pivot->quantity ?? 1) * ($material->pivot->unit_price ?? 0),
                        ];
                    }
                }
            }
        }
        return response()->json([
            'client' => [
                'name' => $quotationRequest->user->name ?? '',
                'address' => $quotationRequest->user->address ?? '',
                'street' => $quotationRequest->user->street ?? '',
                'city' => $quotationRequest->user->city ?? '',
                'state' => $quotationRequest->user->state ?? '',
                'postal' => $quotationRequest->user->postal ?? '',
                'email' => $quotationRequest->user->email ?? '',
                'phone' => $quotationRequest->user->phone ?? '',
            ],
            'property' => $property,
            'items' => $items,
            'scope_of_work' => '',
            'scope_description' => '',
            'payment_terms' => '',
            'warranty_terms' => '',
            'cancellation_terms' => '',
            'additional_terms' => '',
        ]);
    }
}