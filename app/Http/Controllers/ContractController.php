<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Party;
use App\Models\Property;
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
            'items.material.suppliers' => function($query) {
                $query->where('is_preferred', true);
            }
        ]);

        return view('admin.contracts.show', compact('contract'));
    }

    public function create()
    {
        return view('admin.contracts.form', [
            'edit_mode' => false,
            'contract' => null,
            'contractor' => null,
            'client' => null,
            'property' => null,
            'items' => []
        ]);
    }

    public function store(Request $request)
    {
        return $this->saveContract($request);
    }

    public function edit(Contract $contract)
    {
        $contract->load([
            'contractor',
            'client',
            'property',
            'items.material.suppliers' => function($query) {
                $query->where('is_preferred', true);
            }
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
                'scope_of_work' => 'required|array|min:1',
                'scope_description' => 'required|string',
                'budget_allocation' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_terms' => 'required|string',
                'bank_name' => 'required_if:payment_method,bank_transfer',
                'bank_account_name' => 'required_if:payment_method,bank_transfer',
                'bank_account_number' => 'required_if:payment_method,bank_transfer',
            ]);

            DB::beginTransaction();

            try {
                // Save contractor with minimal required fields
                $contractor = Party::updateOrCreate(
                    ['email' => $request->contractor_email],
                    [
                        'type' => 'contractor',
                        'entity_type' => 'person', // Default to person for contractor
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
                    'budget_allocation' => $request->budget_allocation,
                    'payment_method' => $request->payment_method,
                    'payment_terms' => $request->payment_terms,
                    'bank_name' => $request->bank_name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'jurisdiction' => $request->jurisdiction ?? $request->property_city . ', Philippines',
                    'contract_terms' => $request->contract_terms ?? 'Standard terms and conditions apply',
                'client_signature' => $signatures['client'],
                'contractor_signature' => $signatures['contractor'],
                'status' => 'draft'
            ];

            if ($contract) {
                $contract->update($contractData);
            } else {
                $contract = Contract::create($contractData);
            }

                // Save items if present
                if ($request->has('items')) {
            $this->saveItems($request, $contract);
                }

            DB::commit();

            return redirect()->route('contracts.show', $contract->id)
                ->with('success', 'Contract saved successfully');
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

        foreach ($materials as $index => $materialId) {
            if (!$materialId) continue;

            $contract->items()->create([
                'material_id' => $materialId,
                'supplier_id' => $supplierIds[$index] ?? null,
                'quantity' => $quantities[$index],
                'amount' => $amounts[$index],
                'total' => $quantities[$index] * $amounts[$index]
            ]);
        }
    }
} 