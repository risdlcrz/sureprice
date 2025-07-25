<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Party;
use App\Models\Property;
use App\Models\Project;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;
use App\Models\Employee;

class ContractController extends Controller
{
    public function __construct()
    {
        // No need for middleware here as routes are already protected
    }

    public function index()
    {
        $contracts = Contract::with(['contractor', 'client', 'property'])
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract)
    {
        $contract->load([
            'contractor',
            'client',
            'property',
            'items.material',
            'items.supplier',
            'items.room',
            'items.scope',
            'purchaseOrder',
            'purchaseOrder.supplier',
            'rooms.scopes.scopeType',
        ]);

        // Fetch awarded supplier discount if linked to a quotation request
        $awardedSupplierDiscount = null;
        if ($contract->quotation_request_id) {
            $qr = \App\Models\QuotationRequest::find($contract->quotation_request_id);
            if ($qr && $qr->awarded_supplier_id) {
                $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #' . $qr->request_number . '%')->get();
                foreach ($rfqs as $rfq) {
                    $response = \App\Models\QuotationResponse::where('quotation_id', $rfq->id)
                        ->where('supplier_id', $qr->awarded_supplier_id)
                        ->first();
                    if ($response) {
                        $awardedSupplierDiscount = [
                            'discount_type' => $response->discount_type,
                            'discount_percentage' => $response->discount_percentage,
                            'discount_amount' => $response->discount_amount,
                            'final_amount' => $response->final_amount,
                            'total_amount' => $response->total_amount,
                        ];
                        break;
                    }
                }
            }
        }

        return view('admin.contracts.show', compact('contract', 'awardedSupplierDiscount'));
    }

    public function create()
    {
        $contractors = Employee::where('role', 'contractor')->get();
        $quotationRequests = \App\Models\QuotationRequest::doesntHave('contract')->with('user')->orderByDesc('created_at')->get();
        return view('admin.contracts.create', compact('contractors', 'quotationRequests'));
    }

    public function store(Request $request)
    {
        \Log::info('CONTRACT STORE CALLED', $request->all());
        $contractorData = $request->input('contractor', []);
        $clientData = $request->input('client', []);
        $propertyData = $request->input('property', []);
        $contractData = $request->input('contract', []);

        // Validate required fields
        $request->validate([
            'quotation_request_id' => 'required|exists:quotation_requests,id',
            'contractor.name' => 'required|string',
            'contractor.email' => 'required|email',
            'client.name' => 'required|string',
            'client.email' => 'required|email',
            'property.street' => 'required|string',
            'property.city' => 'required|string',
            'property.state' => 'required|string',
            'property.postal' => 'required|string',
            'contract.scope_of_work' => 'required',
            'contract.scope_description' => 'required',
            'contract.payment_terms' => 'required',
            'contract.warranty_terms' => 'required',
            'contract.cancellation_terms' => 'required',
            'contract.additional_terms' => 'required',
            'materials_total' => 'required|numeric',
            'labor_fee' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'payment_plan' => 'required|string',
        ]);

        // Prevent duplicate contracts for the same Quotation Request
        if (\App\Models\Contract::where('quotation_request_id', $request->quotation_request_id)->exists()) {
            return back()->withErrors(['quotation_request_id' => 'A contract already exists for this Quotation Request.'])->withInput();
        }

            DB::beginTransaction();
        try {
            $contractor = Party::updateOrCreate(
                ['email' => $contractorData['email'], 'type' => 'contractor'],
                [
                    'type' => 'contractor',
                    'entity_type' => 'company', // default to company for contractor
                    'name' => $contractorData['name'],
                    'company_name' => $contractorData['company_name'] ?? null,
                    'phone' => $contractorData['phone'] ?? '',
                    'street' => $contractorData['street'] ?? '',
                    'barangay' => $contractorData['barangay'] ?? '',
                    'city' => $contractorData['city'] ?? '',
                    'state' => $contractorData['state'] ?? '',
                    'postal' => $contractorData['postal'] ?? '',
                    'email' => $contractorData['email'],
                ]
            );
            $client = Party::updateOrCreate(
                ['email' => $clientData['email'], 'type' => 'client'],
                [
                    'type' => 'client',
                    'entity_type' => 'person', // default to person for client
                    'name' => $clientData['name'],
                    'company_name' => $clientData['company_name'] ?? null,
                    'phone' => $clientData['phone'] ?? '',
                    'street' => $clientData['street'] ?? '',
                    'barangay' => $clientData['barangay'] ?? '',
                    'city' => $clientData['city'] ?? '',
                    'state' => $clientData['state'] ?? '',
                    'postal' => $clientData['postal'] ?? '',
                    'email' => $clientData['email'],
                ]
            );
            $property = Property::create([
                'street' => $propertyData['street'] ?? '',
                'barangay' => $propertyData['barangay'] ?? '',
                'city' => $propertyData['city'] ?? '',
                'state' => $propertyData['state'] ?? '',
                'postal' => $propertyData['postal'] ?? '',
                'property_address' => $request->input('property_address'),
            ]);
            $quotationRequestId = $request->input('quotation_request_id');
            $quotationRequest = null;
            if ($quotationRequestId) {
                $quotationRequest = \App\Models\QuotationRequest::with(['rooms.scopes.scopeType.materials'])->find($quotationRequestId);
            }
            $awardedSupplierDiscount = null;
            if ($quotationRequest && isset($quotationRequest->contract_data['awarded_supplier_discount'])) {
                $awardedSupplierDiscount = $quotationRequest->contract_data['awarded_supplier_discount'];
            }
            \Log::info('DEBUG awardedSupplierDiscount', [
                'contract_data' => $quotationRequest ? $quotationRequest->contract_data : null,
                'awarded_supplier_discount' => $awardedSupplierDiscount
            ]);
            $contract = Contract::create([
                'quotation_request_id' => $request->input('quotation_request_id'),
                'contractor_id' => $contractor->id,
                'client_id' => $client->id,
                'property_id' => $property->id,
                'title' => 'Contract for ' . $client->name,
                'scope_of_work' => $contractData['scope_of_work'],
                'scope_description' => $contractData['scope_description'],
                'start_date' => $request->input('project_start_date'),
                'end_date' => $request->input('project_end_date'),
                'total_amount' => $request->input('grand_total'),
                'materials_cost' => $request->input('materials_total'),
                'labor_cost' => $request->input('labor_fee'),
                'payment_terms' => $contractData['payment_terms'],
                'payment_method' => $request->input('payment_method'),
                'payment_plan' => $request->input('payment_plan'),
                'bank_name' => $request->input('bank_name'),
                'bank_account_name' => $request->input('bank_account_name'),
                'bank_account_number' => $request->input('bank_account_number'),
                'check_number' => $request->input('check_number'),
                'check_date' => $request->input('check_date'),
                'contractor_signature' => $request->input('contractor_signature'),
                'client_signature' => $request->input('client_signature'),
                'payment_schedule' => $request->input('payment_schedule'),
                'warranty_terms' => $contractData['warranty_terms'],
                'cancellation_terms' => $contractData['cancellation_terms'],
                'additional_terms' => $contractData['additional_terms'],
                'status' => 'draft',
                'property_address' => $request->input('property_address'),
                'discount_type' => $awardedSupplierDiscount['discount_type'] ?? null,
                'discount_percentage' => $awardedSupplierDiscount['discount_percentage'] ?? null,
                'discount_amount' => $awardedSupplierDiscount['discount_amount'] ?? null,
                'final_amount' => $awardedSupplierDiscount['final_amount'] ?? null,
            ]);
            \Log::info('CONTRACT CREATED', [
                'property_address' => $contract->property_address,
                'discount_type' => $contract->discount_type,
                'discount_percentage' => $contract->discount_percentage,
                'discount_amount' => $contract->discount_amount,
                'final_amount' => $contract->final_amount,
            ]);
            // REMINDER: If you update the contract later, always include 'property_address' in the update array!
            // Link to QuotationRequest if provided
            // Copy rooms, scopes, and materials from QuotationRequest
            if ($quotationRequest) {
                // Find all RFQs (Quotations) generated for this QuotationRequest
                $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'. $quotationRequest->request_number .'%')->with('materials')->get();

                // Get the finalized supplier mapping from the quotation request
                $finalizedSuppliers = $quotationRequest->selected_suppliers ?? [];

                foreach ($quotationRequest->rooms as $room) {
                    $newRoom = $contract->rooms()->create([
                        'name' => $room->name,
                        'length' => $room->length,
                        'width' => $room->width,
                        'height' => $room->height,
                        'area' => $room->area,
                        'volume' => $room->volume,
                    ]);
                    foreach ($room->scopes as $scope) {
                        $newScope = $newRoom->scopes()->create([
                            'scope_type_id' => $scope->scope_type_id,
                            'scope_name' => $scope->scope_name,
                            'scope_category' => $scope->scope_category,
                            'selected_materials' => $scope->selected_materials,
                        ]);
                        // Create contract items for each material
                        if (is_array($scope->selected_materials)) {
                            foreach ($scope->selected_materials as $mat) {
                                $materialId = $mat['material_id'] ?? $mat['id'] ?? null;
                                $material = $materialId ? \App\Models\Material::find($materialId) : null;
                                $qty = $mat['quantity'] ?? 1;
                                $unit = $mat['unit'] ?? ($material ? $material->unit : 'pcs');

                                // Only use the supplier that the client finalized
                                $supplierId = $finalizedSuppliers[$materialId] ?? null;
                                $unitPrice = $material ? $material->base_price : 0;
                                if ($supplierId) {
                                    foreach ($rfqs as $rfq) {
                                        $pivot = \DB::table('material_quotation')
                                            ->where('quotation_id', $rfq->id)
                                            ->where('material_id', $materialId)
                                            ->where('selected_supplier_id', $supplierId)
                                            ->first();
                                        if ($pivot && $pivot->unit_price) {
                                            $unitPrice = $pivot->unit_price;
                                            break;
                                        }
                                    }
                                }

                                \Log::info('Creating contract item', [
                                    'room_id' => $newRoom->id ?? null,
                                    'scope_id' => $newScope->id ?? null,
                                    'material' => $materialId,
                                    'room_name' => $newRoom->name ?? null,
                                    'scope_name' => $newScope->scope_name ?? null,
                                    'quotation_request_id' => $quotationRequestId,
                                ]);
                                $contract->items()->create([
                                    'material_id' => $materialId,
                                    'material_name' => $material ? $material->name : ($mat['name'] ?? 'Material'),
                                    'unit' => $unit,
                                    'supplier_id' => $supplierId,
                                    'supplier_name' => $supplierId ? (\App\Models\Supplier::find($supplierId)->company_name ?? 'N/A') : null,
                                    'quantity' => $qty,
                                    'amount' => $unitPrice,
                                    'total' => $qty * $unitPrice,
                                    'room_id' => $newRoom->id,
                                    'scope_id' => $newScope->id,
                                ]);
                            }
                        }
                    }
                }
            }
            // After copying contract items, set awarded_supplier_id on the quotation request if possible
            if ($quotationRequest) {
                $contract->load('items');
                $supplierIds = $contract->items->pluck('supplier_id')->unique()->filter();
                if ($supplierIds->count() === 1) {
                    $quotationRequest->awarded_supplier_id = $supplierIds->first();
                    $quotationRequest->save();
                } else {
                    $quotationRequest->awarded_supplier_id = null;
                    $quotationRequest->save();
                }
            }
            DB::commit();
            // Clear session data after contract creation
            session()->forget('quotation_request_id');
            session()->forget('client_quotation_data');
            return redirect()->route('contracts.show', $contract->id)->with('success', 'Contract created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error saving contract: ' . $e->getMessage()]);
        }
    }

    public function edit(Contract $contract)
    {
        // Prevent editing completed contracts
        if ($contract->status === 'completed') {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Completed contracts cannot be edited.');
        }
        $contract->load(['contractor', 'client', 'property', 'items']);
        $contractors = Employee::where('role', 'contractor')->get();
        // Pass contract and related data to the view
        return view('admin.contracts.create', [
            'contract' => $contract,
            'contractor' => $contract->contractor,
            'client' => $contract->client,
            'property' => $contract->property,
            'items' => $contract->items,
            'contractors' => $contractors,
        ]);
    }

    public function update(Request $request, Contract $contract)
    {
        return $this->saveContract($request, $contract);
    }

    public function download(Contract $contract)
    {
        $contract->load(['contractor', 'client', 'property', 'items.material.suppliers']);
        
        $pdf = PDF::loadView('admin.contracts.pdf', [
            'contract' => $contract,
            'contractor' => $contract->contractor,
            'client' => $contract->client,
            'property' => $contract->property,
            'items' => $contract->items
        ]);
        
        return $pdf->download('contract-' . $contract->id . '.pdf');
    }

    protected function saveContract(Request $request, Contract $contract = null)
    {
        \Log::info('ContractController@saveContract called', ['request' => $request->all()]);
        try {
            // Validate the request with basic required fields
            $validated = $request->validate([
                'contractor_name' => 'required|string|min:2',
                'contractor_email' => 'required|email',
                'contractor_phone' => 'required|string|min:10',
                'contractor_street' => 'required|string',
                'contractor_barangay' => 'required|string',
                'contractor_city' => 'required|string',
                'contractor_state' => 'required|string',
                'contractor_postal' => 'required|string',
                
                'client_name' => 'required|string|min:2',
                'client_email' => 'required|email',
                'client_phone' => 'required|string|min:10',
                'client_street' => 'required|string',
                'client_barangay' => 'required|string',
                'client_city' => 'required|string',
                'client_state' => 'required|string',
                'client_postal' => 'required|string',
                
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'total_amount' => 'required|numeric|min:0',
                'labor_cost' => 'required|numeric|min:0',
                'materials_cost' => 'required|numeric|min:0',
                'scope_of_work' => 'required|array|min:1',
                'scope_description' => 'required|string',
                'payment_method' => 'required|string',
                'payment_terms' => 'required|string',
                'bank_name' => 'required_if:payment_method,bank_transfer',
                'bank_account_name' => 'required_if:payment_method,bank_transfer',
                'bank_account_number' => 'required_if:payment_method,bank_transfer',
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',

                // Contract items validation
                'item_material_name' => 'required|array',
                'item_material_name.*' => 'required|string',
                'item_quantity' => 'required|array',
                'item_quantity.*' => 'required|numeric|min:0.01',
                'item_amount' => 'required|array',
                'item_amount.*' => 'required|numeric|min:0.01',
                'item_supplier_id' => 'nullable|array',
                'item_supplier_id.*' => 'nullable',
                'item_unit' => 'required|array',
                'item_unit.*' => 'required|string',
                'payment_plan' => 'required|string',
            ]);

            DB::beginTransaction();

            try {
                // Save contractor with minimal required fields
                $contractor = Party::updateOrCreate(
                    ['email' => $request->contractor_email, 'type' => 'contractor'],
                    [
                        'type' => 'contractor',
                        'entity_type' => $request->contractor_company ? 'company' : 'person',
                        'name' => $request->contractor_name,
                        'company_name' => $request->contractor_company,
                        'phone' => $request->contractor_phone,
                        'street' => $request->contractor_street,
                        'barangay' => $request->contractor_barangay,
                        'city' => $request->contractor_city,
                        'state' => $request->contractor_state,
                        'postal' => $request->contractor_postal,
                        'email' => $request->contractor_email,
                    ]
                );
            
                // Save client with minimal required fields
                $client = Party::updateOrCreate(
                    ['email' => $request->client_email, 'type' => 'client'],
                    [
                        'type' => 'client',
                        'entity_type' => $request->client_company ? 'company' : 'person',
                        'name' => $request->client_name,
                        'company_name' => $request->client_company,
                        'street' => $request->client_street,
                        'barangay' => $request->client_barangay,
                        'city' => $request->client_city,
                        'state' => $request->client_state,
                        'postal' => $request->client_postal,
                        'email' => $request->client_email,
                        'phone' => $request->client_phone
                    ]
                );
            
                // Save property with basic info
                $property = Property::create([
                    'street' => $request->input('property_street') ?? $request->client_street,
                    'unit_number' => $request->input('property_unit') ?? null,
                    'barangay' => $request->input('property_barangay') ?? $request->client_barangay,
                    'city' => $request->input('property_city') ?? $request->client_city,
                    'state' => $request->input('property_state') ?? $request->client_state,
                    'postal' => $request->input('property_postal') ?? $request->client_postal,
                    'property_type' => $request->input('property_type') ?? null,
                    'property_size' => $request->input('property_size') ?? null
                ]);
            
                // Process signatures if present
                $signatures = [
                    'client' => null,
                    'contractor' => null
                ];
                
                if ($request->has('client_signature')) {
                    $base64_image = $request->input('client_signature');
                    if (strpos($base64_image, 'data:image') === 0) {
                        list($type, $data) = explode(';', $base64_image);
                        list(, $data) = explode(',', $data);
                        $image_data = base64_decode($data);
                        $filename = 'signatures/' . uniqid('client_') . '.png';
                        if (Storage::disk('public')->put($filename, $image_data)) {
                            $signatures['client'] = $filename;
                        }
                    }
                } elseif ($contract && $contract->client_signature) {
                    $signatures['client'] = $contract->client_signature;
                }
                
                if ($request->has('contractor_signature')) {
                    $base64_image = $request->input('contractor_signature');
                    if (strpos($base64_image, 'data:image') === 0) {
                        list($type, $data) = explode(';', $base64_image);
                        list(, $data) = explode(',', $data);
                        $image_data = base64_decode($data);
                        $filename = 'signatures/' . uniqid('contractor_') . '.png';
                        if (Storage::disk('public')->put($filename, $image_data)) {
                            $signatures['contractor'] = $filename;
                        }
                    }
                } elseif ($contract && $contract->contractor_signature) {
                    $signatures['contractor'] = $contract->contractor_signature;
                }
                
                // Save contract with minimal required fields
                $contractData = [
                    'contractor_id' => $contractor->id,
                    'client_id' => $client->id,
                    'property_id' => $property->id,
                    'title' => $request->input('title', 'Contract for ' . $property->name),
                    'scope_of_work' => implode(', ', $request->scope_of_work),
                    'scope_description' => $request->scope_description ?? '',
                    'start_date' => $request->start_date ?? ($contract ? $contract->start_date : null),
                    'end_date' => $request->end_date ?? ($contract ? $contract->end_date : null),
                    'total_amount' => $request->total_amount,
                    'payment_plan' => $request->payment_plan,
                    'labor_cost' => $request->labor_cost,
                    'materials_cost' => $request->materials_cost,
                    'payment_method' => $request->payment_method,
                    'payment_terms' => $request->payment_terms,
                    'bank_name' => $request->bank_name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'check_number' => $request->check_number,
                    'check_date' => $request->check_date,
                    'jurisdiction' => $request->jurisdiction ?? $request->property_city . ', Philippines',
                    'contract_terms' => $request->contract_terms ?? 'Standard terms and conditions apply',
                    'client_signature' => $signatures['client'],
                    'contractor_signature' => $signatures['contractor'],
                    'status' => 'draft',
                    'purchase_order_id' => $request->purchase_order_id
                ];

                \Log::info('Creating contract in saveContract', $contractData);

                if ($contract) {
                    $contract->update($contractData);
                } else {
                    $contract = Contract::create($contractData);
                }

                // If there's no purchase order, create a purchase request with the contract items
                if (!$request->purchase_order_id) {
                    // Generate PR number (format: PR-YYYYMMDD-XXXX)
                    $date = now()->format('Ymd');
                    $lastPR = \App\Models\PurchaseRequest::where('pr_number', 'like', "PR-{$date}-%")
                        ->orderBy('pr_number', 'desc')
                        ->first();
                    
                    $sequence = '0001';
                    if ($lastPR) {
                        $lastSequence = intval(substr($lastPR->pr_number, -4));
                        $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                    }
                    
                    $prNumber = "PR-{$date}-{$sequence}";
                    
                    $purchaseRequest = \App\Models\PurchaseRequest::create([
                        'contract_id' => $contract->id,
                        'pr_number' => $prNumber,
                        'status' => 'pending',
                        'requester_id' => auth()->id(),
                        'department' => 'Procurement',
                        'required_date' => $request->start_date,
                        'purpose' => 'Materials procurement for Contract ' . $contract->id,
                        'notes' => 'Automatically generated from contract ' . $contract->id,
                        'total_amount' => $request->materials_cost
                    ]);

                    // Create purchase request items from the materials list
                    $materialNames = $request->item_material_name;
                    $quantities = $request->item_quantity;
                    $amounts = $request->item_amount;
                    $supplierIds = $request->item_supplier_id ?? [];
                    $units = $request->item_unit;

                    foreach ($materialNames as $index => $materialName) {
                        // Determine category based on material name
                        $categoryId = $this->determineMaterialCategory($materialName);

                        // Find or create the material
                        $material = \App\Models\Material::firstOrCreate(
                            ['name' => $materialName],
                            [
                                'unit' => $units[$index],
                                'code' => 'MAT' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                                'category_id' => $categoryId
                            ]
                        );

                        // Create the purchase request item
                        $purchaseRequest->items()->create([
                            'material_id' => $material->id,
                            'supplier_id' => isset($supplierIds[$index]) ? $supplierIds[$index] : null,
                            'description' => $materialName,
                            'quantity' => $quantities[$index],
                            'unit' => $units[$index],
                            'estimated_unit_price' => $amounts[$index],
                            'total_amount' => $quantities[$index] * $amounts[$index],
                            'notes' => 'From contract ' . $contract->id
                        ]);

                        // Create contract item
                        $contract->items()->create([
                            'material_id' => $material->id,
                            'material_name' => $materialName,
                            'unit' => $units[$index] ?? 'pcs',
                            'supplier_id' => isset($supplierIds[$index]) ? $supplierIds[$index] : null,
                            'supplier_name' => isset($supplierIds[$index]) ? Supplier::find($supplierIds[$index])->company_name : null,
                            'quantity' => $quantities[$index],
                            'amount' => $amounts[$index],
                            'total' => $quantities[$index] * $amounts[$index]
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('contracts.show', $contract->id);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Contract save error: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Error saving contract: ' . $e->getMessage()]);
        }
    }

    protected function saveParty(Request $request, $type)
    {
        $data = [
            'type' => $type,
            'entity_type' => $type === 'client' && $request->filled('company_name') ? 'company' : 'person',
            'name' => $type === 'client' ? 
                ($request->filled('company_name') ? $request->input('company_name') : $request->input('contact_person')) :
                $request->input('contractor_name'),
            'company_name' => $type === 'client' ? $request->input('company_name') : $request->input('contractor_company'),
            'street' => $request->input("{$type}_street"),
            'city' => $request->input("{$type}_city"),
            'state' => $request->input("{$type}_state"),
            'postal' => $request->input("{$type}_postal"),
            'email' => $request->input("{$type}_email"),
            'phone' => $request->input("{$type}_phone")
        ];

        return Party::updateOrCreate(
            ['email' => $data['email'], 'type' => $type],
            $data
        );
    }

    protected function saveProperty(Request $request)
    {
        return Property::create([
            'street' => $request->input('property_street'),
            'city' => $request->input('property_city'),
            'state' => $request->input('property_state'),
            'postal' => $request->input('property_postal')
        ]);
    }

    protected function handleSignatures(Request $request)
    {
        $signatures = [
            'client' => null,
            'contractor' => null
        ];

        foreach (['client', 'contractor'] as $type) {
            // Keep existing signature if checkbox is checked
            if ($request->has("keep_{$type}_signature") && $request->input("keep_{$type}_signature")) {
                $existingPath = $request->input("existing_{$type}_signature");
                if ($existingPath) {
                    $signatures[$type] = str_replace('/storage/', '', $existingPath);
            }
                continue;
            }
            
            // Handle base64 signature data
            if ($request->has("{$type}_signature")) {
                $base64_image = $request->input("{$type}_signature");
                
                // Check if this is a base64 image
                if (strpos($base64_image, 'data:image') === 0) {
                    // Extract the actual base64 data
                    list($type, $data) = explode(';', $base64_image);
                    list(, $data) = explode(',', $data);
                    
                    // Decode and save the image
                    $image_data = base64_decode($data);
                    $filename = 'signatures/' . uniqid($type . '_') . '.png';
                    
                    if (Storage::disk('public')->put($filename, $image_data)) {
                        $signatures[$type] = $filename;
                    }
                }
                // If it's a file upload
                else if ($request->hasFile("{$type}_signature")) {
                    $path = $request->file("{$type}_signature")->store('signatures', 'public');
                $signatures[$type] = $path;
                }
            }
        }

        return $signatures;
    }

    protected function saveItems(Request $request, Contract $contract)
    {
        // Delete existing items if updating
        if ($contract->items()->exists()) {
            $contract->items()->delete();
        }

        $materials = $request->input('item_material_id', []);
        $quantities = $request->input('item_quantity', []);
        $amounts = $request->input('item_amount', []);
        $supplierIds = $request->input('item_supplier_id', []);
        $supplierNames = $request->input('item_supplier_name', []);
        $units = $request->input('item_unit', []);

        foreach ($materials as $index => $materialId) {
            if (!$materialId) continue;

            // Get material and supplier details
            $material = Material::find($materialId);
            $supplier = null;
            // Fallback: if supplier_id is not set, auto-select preferred or first supplier
            if (empty($supplierIds[$index]) && $material) {
                $preferredSupplier = $material->suppliers()->wherePivot('is_preferred', true)->first() ?? $material->suppliers()->first();
                $supplierIds[$index] = $preferredSupplier ? $preferredSupplier->id : null;
            }
            if (!empty($supplierIds[$index])) {
                $supplier = Supplier::find($supplierIds[$index]);
            }

            if ($material) {
                // Ensure we have a unit value, with fallbacks
                $unit = $units[$index] ?? $material->unit ?? 'pcs';
                
                $contract->items()->create([
                    'material_id' => $materialId,
                    'material_name' => $material->name,
                    'unit' => $unit,
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'supplier_name' => $supplier ? $supplier->company_name : null,
                    'quantity' => $quantities[$index],
                    'amount' => $amounts[$index],
                    'total' => $quantities[$index] * $amounts[$index]
                ]);
            }
        }
    }

    public function destroy(Contract $contract)
    {
        // Prevent deleting completed contracts
        if ($contract->status === 'completed') {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'Completed contracts cannot be deleted.');
        }

        try {
            DB::beginTransaction();
            
            // Delete associated signatures if they exist
            if ($contract->client_signature) {
                Storage::disk('public')->delete($contract->client_signature);
            }
            if ($contract->contractor_signature) {
                Storage::disk('public')->delete($contract->contractor_signature);
            }
            
            // Delete the contract and its relationships
            $contract->items()->delete();
            $contract->delete();
            
            DB::commit();
            
            return redirect()->route('contracts.index')
                ->with('success', 'Contract deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('contracts.index')
                ->with('error', 'Error deleting contract: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Contract $contract)
    {
        // Prevent status changes for completed contracts
        if ($contract->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed contracts cannot have their status changed.'
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,active,approved,rejected,partially_paid,fully_paid,overdue,suspended,terminated,expired,renewed'
        ]);
        $oldStatus = $contract->status;
        $contract->status = $request->status;
        $contract->save();
        try {
            DB::beginTransaction();

            // Require both signatures before approval
            if ($request->status === 'approved') {
                if (empty($contract->contractor_signature) || empty($contract->client_signature)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Both contractor and client signatures are required before approval.'
                    ], 422);
                }
            }

            // Generate payment schedule when contract is approved
            if ($request->status === 'approved') {
                \Log::info('Generating payment schedule for contract: ' . $contract->id);
                
                // Generate payment schedule based on payment terms
                $paymentSchedule = [];
                
                if (strpos($contract->payment_terms, 'Pay All In') !== false) {
                    // Single payment at project completion
                    $paymentSchedule[] = [
                        'stage' => 'Full Payment',
                        'amount' => $contract->total_amount,
                        'due_date' => $contract->end_date->format('Y-m-d')
                    ];
                }
                else if (strpos($contract->payment_terms, 'Progress Payment') !== false) {
                    // Progress payment with advance payment and retention
                    $advancePayment = $contract->total_amount * 0.15; // 15% advance payment
                    $retention = $contract->total_amount * 0.10; // 10% retention
                    $progressPayment = $contract->total_amount - $advancePayment - $retention;
                    
                    // Add advance payment (due at start)
                    $paymentSchedule[] = [
                        'stage' => 'Advance Payment (15%)',
                        'amount' => $advancePayment,
                        'due_date' => $contract->start_date->format('Y-m-d')
                    ];
                    
                    // Add progress payment (due at completion)
                    $paymentSchedule[] = [
                        'stage' => 'Progress Payment (75%)',
                        'amount' => $progressPayment,
                        'due_date' => $contract->end_date->format('Y-m-d')
                    ];
                    
                    // Add retention (due 30 days after completion)
                    $retentionDueDate = $contract->end_date->copy()->addDays(30);
                    $paymentSchedule[] = [
                        'stage' => 'Retention (10%)',
                        'amount' => $retention,
                        'due_date' => $retentionDueDate->format('Y-m-d')
                    ];
                }
                else if (strpos($contract->payment_terms, 'Installment') !== false) {
                    // Parse installment terms (e.g., "30% downpayment, 6 months")
                    if (preg_match('/(\d+)% downpayment, (\d+) months/', $contract->payment_terms, $matches)) {
                        $downpaymentPercent = intval($matches[1]);
                        $months = intval($matches[2]);
                        
                        $downpayment = ($contract->total_amount * $downpaymentPercent) / 100;
                        $remainingAmount = $contract->total_amount - $downpayment;
                        $monthlyPayment = $remainingAmount / $months;
                        
                        // Add downpayment
                        $paymentSchedule[] = [
                            'stage' => "Downpayment ({$downpaymentPercent}%)",
                            'amount' => $downpayment,
                            'due_date' => $contract->start_date->format('Y-m-d')
                        ];
                        
                        // Add monthly installments
                        $installmentDate = $contract->start_date->copy();
                        for ($i = 1; $i <= $months; $i++) {
                            $installmentDate->addMonth();
                            $paymentSchedule[] = [
                                'stage' => "Installment {$i}",
                                'amount' => $monthlyPayment,
                                'due_date' => $installmentDate->format('Y-m-d')
                            ];
                        }
                    }
                }
                
                \Log::info('Generated payment schedule:', $paymentSchedule);
                
                // Set the payment schedule
                $contract->payment_schedule = json_encode($paymentSchedule);
            }

            $contract->save();

            \Log::info('Contract status updated', [
                'contract_id' => $contract->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'payment_terms' => $contract->payment_terms,
                'total_amount' => $contract->total_amount
            ]);

            // Generate payments when contract is approved
            if ($request->status === 'approved') {
                \Log::info('Attempting to generate payments');
                $contract->generatePayments();

                // Auto-create project if not exists
                if (!\App\Models\Project::where('contract_id', $contract->id)->exists()) {
                    $project = \App\Models\Project::create([
                        'project_number' => \App\Models\Project::generateProjectNumber(),
                        'contract_id' => $contract->id,
                        'name' => $contract->title ?? 'Project for Contract ' . $contract->contract_number,
                        'description' => $contract->scope_description ?? '',
                        'start_date' => $contract->start_date,
                        'end_date' => $contract->end_date,
                        'status' => 'pending',
                        'progress' => 0,
                        'project_manager_id' => $contract->contractor_id, // or assign as needed
                        'client_representative_id' => $contract->client_id, // or assign as needed
                        'budget' => $contract->total_amount,
                        'notes' => $contract->scope_of_work ?? '',
                    ]);

                    // Generate project tasks from contract scopes/rooms
                    foreach ($contract->rooms as $room) {
                        foreach ($room->scopeTypes as $scopeType) {
                            $duration = $scopeType->estimated_days ?? 7;
                            $startDate = $contract->start_date;
                            $endDate = $startDate->copy()->addDays($duration);
                            \App\Models\ProjectTask::create([
                                'project_id' => $project->id,
                                'name' => $scopeType->name . ' in ' . $room->name,
                                'description' => 'Complete ' . $scopeType->name . ' work in ' . $room->name,
                                'start_date' => $startDate,
                                'due_date' => $endDate,
                                'status' => 'pending',
                                'priority' => 'medium',
                                'progress' => 0,
                                'notes' => null,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contract status updated successfully',
                'status' => ucfirst($contract->status)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating contract status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating contract status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function timeline(Request $request)
    {
        try {
            $query = Contract::with(['client', 'contractor']);

            // Apply search filter
            if ($request->has('term')) {
                $searchTerm = $request->term;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('contract_id', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('client', function($q_client) use ($searchTerm) {
                          $q_client->where('name', 'like', '%' . $searchTerm . '%');
                      });
                });
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $statuses = explode(',', $request->status);
                $query->whereIn('status', $statuses);
            }

            // Apply date range filter
            if ($request->has('startDate')) {
                $query->where('start_date', '>=', $request->startDate);
            }
            if ($request->has('endDate')) {
                $query->where('end_date', '<=', $request->endDate);
            }

            // Apply budget range filter
            if ($request->has('minBudget')) {
                $query->where('total_amount', '>=', $request->minBudget);
            }
            if ($request->has('maxBudget')) {
                $query->where('total_amount', '<=', $request->maxBudget);
            }

            $contracts = $query->get()->map(function($contract) {
                $safeStatus = $contract->status ?: 'default';

                // Prepare data for FullCalendar
                $calendarEvent = [
                    'id' => 'contract-' . $contract->id,
                    'title' => $contract->client->name ?? 'Unknown Client',
                    'start' => $contract->start_date->format('Y-m-d'),
                    'end' => $contract->end_date->addDay()->format('Y-m-d'),
                    'className' => 'status-' . $safeStatus,
                    'color' => $this->getContractColor($contract),
                    'extendedProps' => [
                        'type' => 'contract',
                        'contract_id' => $contract->contract_id,
                        'client' => $contract->client->name ?? 'Unknown Client',
                        'contractor' => $contract->contractor->name ?? 'N/A',
                        'status' => $safeStatus,
                        'budget' => $contract->total_amount,
                        'scope' => $contract->scope_of_work,
                    ]
                ];

                // Prepare data for Gantt chart
                $ganttTask = [
                    'id' => 'contract-' . $contract->id,
                    'name' => $contract->client->name ?? 'Unknown Client',
                    'start' => $contract->start_date->format('Y-m-d'),
                    'end' => $contract->end_date->format('Y-m-d'),
                    'progress' => match($safeStatus) {
                        'approved' => 100,
                        'draft' => 50,
                        'rejected' => 0,
                        default => 0
                    },
                    'dependencies' => '',
                    'bar_color' => $this->getContractColor($contract)
                ];

                return [
                    'calendar' => $calendarEvent,
                    'gantt' => $ganttTask
                ];
            });

            return response()->json([
                'calendar' => $contracts->pluck('calendar'),
                'gantt' => $contracts->pluck('gantt')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in ContractController timeline: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching timeline data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function projectTimeline()
    {
        $contracts = Contract::with(['client', 'contractor', 'tasks'])
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate overall project progress
        $totalProgress = 0;
        $totalTasks = 0;

        foreach ($contracts as $contract) {
            foreach ($contract->tasks as $task) {
                $totalProgress += $task->progress;
                $totalTasks++;
            }
        }

        $overallProgress = $totalTasks > 0 ? round(($totalProgress / $totalTasks), 2) : 0;

        $mappedContracts = $contracts->map(function($contract) {
            $safeStatus = $contract->status ?: 'default'; // Ensure status is never empty or null

            // Calculate individual contract progress
            $contractTasksCount = $contract->tasks->count();
            $contractTotalProgress = $contractTasksCount > 0 ? $contract->tasks->sum('progress') : 0;
            $individualContractProgress = $contractTasksCount > 0 ? round(($contractTotalProgress / $contractTasksCount), 2) : 0;

            // Prepare data for FullCalendar
            $calendarEvent = [
                'id' => $contract->id,
                'title' => $contract->client->name ?? 'Unknown Client', // Client name as event title
                'start' => $contract->start_date->format('Y-m-d'),
                'end' => $contract->end_date->addDay()->format('Y-m-d'), // FullCalendar end date is exclusive
                'className' => 'status-' . $safeStatus, // Custom class for status styling
                'color' => $this->getContractColor($contract),
                'extendedProps' => [
                    'type' => 'contract', // Moved type to extendedProps
                    'contract_id' => $contract->contract_id,
                    'client' => $contract->client->name ?? 'Unknown Client',
                    'contractor' => $contract->contractor->name ?? 'N/A',
                    'status' => $safeStatus, // Use safe status in extended props
                    'budget' => $contract->total_amount,
                    'scope' => $contract->scope_of_work,
                    'progress' => $individualContractProgress, // Add individual contract progress
                ]
            ];

            // Prepare data for Frappe Gantt
            $ganttTask = [
                'id' => 'contract-' . $contract->id, // Unique ID for Gantt
                'name' => $contract->client->name ?? 'Unknown Client', // Simplified Gantt task name
                'start' => $contract->start_date->format('YYYY-MM-DD'),
                'end' => $contract->end_date->format('YYYY-MM-DD'),
                'progress' => $individualContractProgress, // Use individual contract progress for Gantt
                'dependencies' => [],
                'bar_color' => $this->getContractColor($contract) // Add bar_color for distinctness
            ];

            return [
                'calendar' => $calendarEvent,
                'gantt' => $ganttTask
            ];
        });

        // Pass both calendar events and gantt tasks to the view
        return view('admin.contracts.timeline', [
            'contracts' => $mappedContracts->pluck('calendar')->toJson(), // FullCalendar expects JSON
            'ganttTasks' => $mappedContracts->pluck('gantt')->toJson(), // Gantt expects JSON
            'overallProgress' => $overallProgress // Pass overall progress
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        
        if (empty($term) || strlen($term) < 2) {
            return response()->json([]);
        }

        $contracts = Contract::with(['client', 'contractor'])
            ->where(function ($query) use ($term) {
                $query->where('contract_id', 'like', "%{$term}%")
                    ->orWhere('scope_of_work', 'like', "%{$term}%")
                    ->orWhereHas('client', function ($q) use ($term) {
                        $q->where(DB::raw("CONCAT(name, ' ', COALESCE(company_name, ''))"), 'like', "%{$term}%");
                    })
                    ->orWhereHas('contractor', function ($q) use ($term) {
                        $q->where(DB::raw("CONCAT(name, ' ', COALESCE(company_name, ''))"), 'like', "%{$term}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'contract_id' => $contract->contract_id,
                    'client_name' => $contract->client->name,
                    'client_company' => $contract->client->company_name,
                    'contractor_name' => $contract->contractor->name,
                    'contractor_company' => $contract->contractor->company_name,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'status' => $contract->status,
                    'budget_allocation' => $contract->budget_allocation,
                    'scope_of_work' => $contract->scope_of_work
                ];
            });

        return response()->json($contracts);
    }

    protected function determineMaterialCategory($materialName)
    {
        $materialName = strtolower($materialName);
        
        // Construction materials
        if (str_contains($materialName, 'concrete') || 
            str_contains($materialName, 'cement') || 
            str_contains($materialName, 'steel') ||
            str_contains($materialName, 'wood') ||
            str_contains($materialName, 'lumber') ||
            str_contains($materialName, 'drywall') ||
            str_contains($materialName, 'structural')) {
            return \App\Models\Category::where('slug', 'construction')->first()->id;
        }
        
        // Electrical materials
        if (str_contains($materialName, 'wire') || 
            str_contains($materialName, 'electrical') || 
            str_contains($materialName, 'socket') ||
            str_contains($materialName, 'switch') ||
            str_contains($materialName, 'circuit')) {
            return \App\Models\Category::where('slug', 'electrical')->first()->id;
        }
        
        // Plumbing materials
        if (str_contains($materialName, 'pipe') || 
            str_contains($materialName, 'plumbing') || 
            str_contains($materialName, 'valve') ||
            str_contains($materialName, 'fitting')) {
            return \App\Models\Category::where('slug', 'plumbing')->first()->id;
        }
        
        // Finishing materials
        if (str_contains($materialName, 'paint') || 
            str_contains($materialName, 'tile') || 
            str_contains($materialName, 'finish') || 
            str_contains($materialName, 'ceiling') ||
            str_contains($materialName, 'floor')) {
            return \App\Models\Category::where('slug', 'finishing')->first()->id;
        }
        
        // Tools and equipment
        if (str_contains($materialName, 'tool') || 
            str_contains($materialName, 'equipment') || 
            str_contains($materialName, 'machine') ||
            str_contains($materialName, 'safety')) {
            return \App\Models\Category::where('slug', 'tools')->first()->id;
        }
        
        // Default to 'Other' category
        return \App\Models\Category::where('slug', 'other')->first()->id;
    }

    public function saveSignature(Request $request)
    {
        try {
            $type = $request->input('type');
            $signature = $request->input('signature');
            
            if (!$signature || !$type) {
                return response()->json(['success' => false, 'message' => 'Missing signature or type']);
            }

            // Save signature to storage if it's a base64 image
            if (strpos($signature, 'data:image') === 0) {
                list($type, $data) = explode(';', $signature);
                list(, $data) = explode(',', $data);
                $image_data = base64_decode($data);
                $filename = 'signatures/' . uniqid($type . '_') . '.png';
                
                if (Storage::disk('public')->put($filename, $image_data)) {
                    // Store the path in session
                    session(['contract_step3.' . $type . '_signature' => $filename]);
                    return response()->json(['success' => true, 'path' => $filename]);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'Invalid signature format']);
        } catch (\Exception $e) {
            \Log::error('Error saving signature: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving signature']);
        }
    }

    /**
     * Update signatures for an existing contract
     */
    public function updateSignatures(Request $request, Contract $contract)
    {
        try {
            $request->validate([
                'contractor_signature' => 'nullable|string',
                'client_signature' => 'nullable|string',
                'signature_type' => 'required|in:contractor,client'
            ]);

            $signatureType = $request->input('signature_type');
            $signatureData = $request->input($signatureType . '_signature');

            if ($signatureData && strpos($signatureData, 'data:image') === 0) {
                list(, $data) = explode(',', $signatureData);
                $image_data = base64_decode($data);
                $filename = 'signatures/' . uniqid($signatureType . '_') . '.png';
                
                if (Storage::disk('public')->put($filename, $image_data)) {
                    $contract->update([
                        $signatureType . '_signature' => $filename,
                        $signatureType . '_date_signed' => now()
                    ]);
                    
                    return response()->json([
                        'success' => true, 
                        'message' => ucfirst($signatureType) . ' signature updated successfully',
                        'signature_path' => $filename
                    ]);
                }
            }

            return response()->json(['success' => false, 'message' => 'Invalid signature format']);
        } catch (\Exception $e) {
            \Log::error('Error updating signature: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating signature']);
        }
    }

    /**
     * API: Get all items (materials) for a contract
     */
    public function getItems($contractId)
    {
        $contract = Contract::with(['items.material.suppliers', 'items.supplier'])->findOrFail($contractId);
        return response()->json($contract->items);
    }

    protected function getContractColor($contract)
    {
        $colors = ['#3490dc', '#6574cd', '#9561e2', '#f66d9b', '#e3342f'];
        return $colors[$contract->id % count($colors)];
    }
} 