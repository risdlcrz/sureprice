<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Party;
use App\Models\Property;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

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
            'purchaseOrder',
            'purchaseOrder.supplier'
        ]);

        return view('admin.contracts.show', compact('contract'));
    }

    public function create()
    {
        $purchaseOrders = \App\Models\PurchaseOrder::where('status', 'approved')
            ->where(function($query) {
                $query->whereNull('contract_id')
                    ->orWhere('contract_id', 0);
            })
            ->get();
        return view('admin.contracts.form', [
            'edit_mode' => false,
            'contract' => null,
            'contractor' => null,
            'client' => null,
            'property' => null,
            'items' => [],
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    public function store(Request $request)
    {
        return $this->saveContract($request);
    }

    public function edit(Contract $contract)
    {
        $purchaseOrders = \App\Models\PurchaseOrder::where('status', 'approved')
            ->where(function($query) use ($contract) {
                $query->whereNull('contract_id')
                    ->orWhere('contract_id', 0)
                    ->orWhere('id', $contract->purchase_order_id);
            })
            ->get();
        $contract->load([
            'contractor',
            'client',
            'property',
            'items.material.suppliers'
        ]);

        return view('admin.contracts.form', [
            'edit_mode' => true,
            'contract' => $contract,
            'contractor' => $contract->contractor,
            'client' => $contract->client,
            'property' => $contract->property,
            'items' => $contract->items,
            'existing_client_signature' => $contract->client_signature ? Storage::url($contract->client_signature) : null,
            'existing_contractor_signature' => $contract->contractor_signature ? Storage::url($contract->contractor_signature) : null,
            'purchaseOrders' => $purchaseOrders
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
            ]);

            DB::beginTransaction();

            try {
                // Save contractor with minimal required fields
                $contractor = Party::updateOrCreate(
                    ['email' => $request->contractor_email],
                    [
                        'type' => 'contractor',
                        'entity_type' => 'person',
                        'name' => $request->contractor_name,
                        'street' => $request->contractor_street,
                        'barangay' => $request->contractor_barangay,
                        'city' => $request->contractor_city,
                        'state' => $request->contractor_state,
                        'postal' => $request->contractor_postal,
                        'email' => $request->contractor_email,
                        'phone' => $request->contractor_phone
                    ]
                );
            
                // Save client with minimal required fields
                $client = Party::updateOrCreate(
                    ['email' => $request->client_email],
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
                    'street' => $request->property_street ?? $request->client_street,
                    'unit_number' => $request->property_unit ?? null,
                    'barangay' => $request->property_barangay ?? $request->client_barangay,
                    'city' => $request->property_city ?? $request->client_city,
                    'state' => $request->property_state ?? $request->client_state,
                    'postal' => $request->property_postal ?? $request->client_postal,
                    'property_type' => $request->property_type ?? null,
                    'property_size' => $request->property_size ?? null
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
                    'scope_of_work' => implode(', ', $request->scope_of_work),
                    'scope_description' => $request->scope_description ?? '',
                    'start_date' => $request->start_date ?? ($contract ? $contract->start_date : null),
                    'end_date' => $request->end_date ?? ($contract ? $contract->end_date : null),
                    'total_amount' => $request->total_amount,
                    'labor_cost' => $request->labor_cost,
                    'materials_cost' => $request->materials_cost,
                    'payment_method' => $request->payment_method,
                    'payment_terms' => $request->payment_terms,
                    'bank_name' => $request->bank_name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'jurisdiction' => $request->jurisdiction ?? $request->property_city . ', Philippines',
                    'contract_terms' => $request->contract_terms ?? 'Standard terms and conditions apply',
                    'client_signature' => $signatures['client'],
                    'contractor_signature' => $signatures['contractor'],
                    'status' => 'draft',
                    'purchase_order_id' => $request->purchase_order_id
                ];

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
                    'supplier_id' => $supplierIds[$index] ?? null,
                    'supplier_name' => $supplierNames[$index] ?? ($supplier ? $supplier->name : null),
                    'quantity' => $quantities[$index],
                    'amount' => $amounts[$index],
                    'total' => $quantities[$index] * $amounts[$index]
                ]);
            }
        }
    }

    public function destroy(Contract $contract)
    {
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

    public function updateStatus(Contract $contract, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,approved,rejected'
            ]);

            $contract->status = $request->status;
            $contract->save();

            return response()->json([
                'success' => true,
                'message' => 'Contract status updated successfully',
                'status' => ucfirst($contract->status)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating contract status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function timeline()
    {
        $contracts = Contract::with(['client', 'contractor'])
            ->get()
            ->map(function($contract) {
                return [
                    'id' => $contract->id,
                    'contract_id' => $contract->contract_id,
                    'title' => $contract->client->name . ' - ' . $contract->contract_id,
                    'start' => $contract->start_date->format('Y-m-d'),
                    'end' => $contract->end_date->format('Y-m-d'),
                    'backgroundColor' => match($contract->status) {
                        'draft' => '#ffc107',     // warning
                        'approved' => '#198754',   // success
                        'rejected' => '#dc3545',   // danger
                        default => '#6c757d'       // secondary
                    },
                    'borderColor' => match($contract->status) {
                        'draft' => '#ffc107',
                        'approved' => '#198754',
                        'rejected' => '#dc3545',
                        default => '#6c757d'
                    },
                    'extendedProps' => [
                        'client' => $contract->client->name,
                        'contractor' => $contract->contractor->name,
                        'scope' => $contract->scope_of_work,
                        'budget' => $contract->budget_allocation,
                        'status' => $contract->status,
                        'contract_id' => $contract->contract_id
                    ]
                ];
            });

        return response()->json($contracts);
    }

    public function projectTimeline()
    {
        $contracts = Contract::with(['client', 'contractor', 'project'])
            ->orderBy('start_date')
            ->get();
            
        $allContracts = Contract::with(['client', 'contractor'])
            ->orderBy('contract_id')
            ->get();
            
        return view('admin.project-timeline', compact('contracts', 'allContracts'));
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
} 