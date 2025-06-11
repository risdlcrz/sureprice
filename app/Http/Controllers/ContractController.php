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

    public function clearContractSession()
    {
        session()->forget(['contract_step1', 'contract_step2', 'contract_step3']);
        return redirect()->route('contracts.index')->with('success', 'Contract creation cancelled. All data has been cleared.');
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
        // Clear all contract wizard session data
        session()->forget([
            'contract_step1',
            'contract_step2',
            'contract_step3',
            'contract_step4',
            'step3_data',
            'step4_data'
        ]);
        return view('admin.contracts.step1');
    }

    public function step1()
    {
        // Do NOT clear session here! Only show the view.
        return view('admin.contracts.step1');
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'contractor_name' => 'required|string|max:255',
            'contractor_company' => 'nullable|string|max:255',
            'contractor_email' => 'required|email|max:255',
            'contractor_phone' => 'required|string|max:20',
            'contractor_street' => 'required|string|max:255',
            'contractor_barangay' => 'required|string|max:255',
            'contractor_city' => 'required|string|max:255',
            'contractor_state' => 'required|string|max:255',
            'contractor_postal' => 'required|string|max:20',
            
            'client_name' => 'required|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'client_street' => 'required|string|max:255',
            'client_unit' => 'nullable|string|max:255',
            'client_barangay' => 'required|string|max:255',
            'client_city' => 'required|string|max:255',
            'client_state' => 'required|string|max:255',
            'client_postal' => 'required|string|max:20',
            
            'property_type' => 'required|string|in:residential,commercial,industrial',
            'property_street' => 'required|string|max:255',
            'property_unit' => 'nullable|string|max:255',
            'property_barangay' => 'required|string|max:255',
            'property_city' => 'required|string|max:255',
            'property_state' => 'required|string|max:255',
            'property_postal' => 'required|string|max:20',
        ]);

        // Store in session
        session(['contract_step1' => $validated]);

        return redirect()->route('contracts.step2');
    }

    public function saveStep1(Request $request)
    {
        // Store the current state in session without validation
        session([
            'contract_step1' => [
                'contractor_name' => $request->input('contractor_name'),
                'contractor_company' => $request->input('contractor_company'),
                'contractor_email' => $request->input('contractor_email'),
                'contractor_phone' => $request->input('contractor_phone'),
                'contractor_street' => $request->input('contractor_street'),
                'contractor_barangay' => $request->input('contractor_barangay'),
                'contractor_city' => $request->input('contractor_city'),
                'contractor_state' => $request->input('contractor_state'),
                'contractor_postal' => $request->input('contractor_postal'),
                
                'client_name' => $request->input('client_name'),
                'client_company' => $request->input('client_company'),
                'client_email' => $request->input('client_email'),
                'client_phone' => $request->input('client_phone'),
                'client_street' => $request->input('client_street'),
                'client_unit' => $request->input('client_unit'),
                'client_barangay' => $request->input('client_barangay'),
                'client_city' => $request->input('client_city'),
                'client_state' => $request->input('client_state'),
                'client_postal' => $request->input('client_postal'),
                
                'property_type' => $request->input('property_type'),
                'property_street' => $request->input('property_street'),
                'property_unit' => $request->input('property_unit'),
                'property_barangay' => $request->input('property_barangay'),
                'property_city' => $request->input('property_city'),
                'property_state' => $request->input('property_state'),
                'property_postal' => $request->input('property_postal')
            ]
        ]);

        return response()->json(['success' => true]);
    }

    public function step2()
    {
        // Get scope types with materials through relationship
        $scopeTypes = \App\Models\ScopeType::with(['materials' => function($query) {
            $query->with(['suppliers' => function($q) {
                $q->wherePivot('is_preferred', true);
            }]);
        }])->get();

        // Get session data if it exists
        $sessionData = session('contract_step2', []);
        
        // Ensure rooms is always an array, not an object
        if (isset($sessionData['rooms']) && !is_array($sessionData['rooms'])) {
            // Convert object to array
            $rooms = [];
            foreach ($sessionData['rooms'] as $roomId => $roomData) {
                $roomData['id'] = $roomId;
                $rooms[] = $roomData;
            }
            $sessionData['rooms'] = $rooms;
        }

        \Log::info('Loading Step 2 with session data:', [
            'has_session_data' => !empty($sessionData),
            'session_data' => $sessionData,
            'all_session_keys' => array_keys(session()->all())
        ]);

        return view('admin.contracts.step2', compact('scopeTypes', 'sessionData'));
    }

    public function storeStep2(Request $request)
    {
        try {
            $validated = $request->validate([
                'rooms' => 'required|array',
                'rooms.*.name' => 'required|string|max:255',
                'rooms.*.length' => 'required|numeric|min:0.1',
                'rooms.*.width' => 'required|numeric|min:0.1',
                'rooms.*.area' => 'required|numeric|min:0',
                'rooms.*.scope' => 'required|array',
                'rooms.*.scope.*' => 'required|string',
                'rooms.*.materials_cost' => 'nullable|numeric|min:0',
                'rooms.*.labor_cost' => 'nullable|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'total_materials' => 'required|numeric|min:0',
                'total_labor' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
            ]);

            // Store in session
            $sessionData = [
                'rooms' => $validated['rooms'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_materials' => $validated['total_materials'],
                'total_labor' => $validated['total_labor'],
                'grand_total' => $validated['grand_total'],
                'total_amount' => $validated['grand_total'],
                'labor_cost' => $validated['total_labor'],
                'materials_cost' => $validated['total_materials']
            ];
            
            session(['contract_step2' => $sessionData]);
            \Log::info('Saved Step 2 data to session:', $sessionData);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('contracts.step3');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in Step 2:', $e->errors());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in Step 2:', ['error' => $e->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the data.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while saving the data.')->withInput();
        }
    }

    public function saveStep2(Request $request)
    {
        try {
            // Get the input data
            $data = $request->all();
            
            // Ensure rooms is an array
            if (isset($data['rooms']) && !is_array($data['rooms'])) {
                $rooms = [];
                foreach ($data['rooms'] as $roomId => $roomData) {
                    $roomData['id'] = $roomId;
                    $rooms[] = $roomData;
                }
                $data['rooms'] = $rooms;
            }

            // Store in session
            $sessionData = [
                'rooms' => $data['rooms'] ?? [],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'total_materials' => $data['total_materials'] ?? 0,
                'total_labor' => $data['total_labor'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'total_amount' => $data['grand_total'] ?? 0,
                'labor_cost' => $data['total_labor'] ?? 0,
                'materials_cost' => $data['total_materials'] ?? 0
            ];
            
            session(['contract_step2' => $sessionData]);
            \Log::info('Auto-saved Step 2 data to session:', $sessionData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error in Step 2 auto-save:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the data.'
            ], 500);
        }
    }

    public function step3()
    {
        if (!session()->has('contract_step2')) {
            return redirect()->route('contracts.create');
        }

        return view('admin.contracts.step3');
    }

    public function storeStep3(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_terms' => 'required|string',
                'warranty_terms' => 'required|string',
                'cancellation_terms' => 'required|string',
                'additional_terms' => 'nullable|string',
                'contractor_signature' => 'required|string',
                'client_signature' => 'required|string',
            ]);

            // Process signatures
            foreach (['contractor', 'client'] as $type) {
                $signatureData = $request->input($type . '_signature');
                if (strpos($signatureData, 'data:image') === 0) {
                    list(, $data) = explode(',', $signatureData);
                    $image_data = base64_decode($data);
                    $filename = 'signatures/' . uniqid($type . '_') . '.png';
                    
                    if (Storage::disk('public')->put($filename, $image_data)) {
                        $validated[$type . '_signature'] = $filename;
                    }
                }
            }

            // Store in session
            session(['contract_step3' => $validated]);
            session(['step3_data' => $validated]);

            return redirect()->route('contracts.step4');
        } catch (\Exception $e) {
            \Log::error('Error in storeStep3: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error saving contract data: ' . $e->getMessage()]);
        }
    }

    public function saveStep3(Request $request)
    {
        try {
            $data = $request->validate([
                'payment_terms' => 'nullable|string',
                'warranty_terms' => 'nullable|string',
                'cancellation_terms' => 'nullable|string',
                'additional_terms' => 'nullable|string',
                'contractor_signature' => 'nullable|string',
                'client_signature' => 'nullable|string'
            ]);

            // Process signatures if they are new data URLs
            foreach (['contractor', 'client'] as $type) {
                $signatureKey = $type . '_signature';
                $signatureData = $data[$signatureKey] ?? null;
                
                // If it's a new signature (data URL)
                if ($signatureData && strpos($signatureData, 'data:image') === 0) {
                    list(, $imageData) = explode(',', $signatureData);
                    $decodedData = base64_decode($imageData);
                    $filename = 'signatures/' . uniqid($type . '_') . '.png';
                    
                    if (Storage::disk('public')->put($filename, $decodedData)) {
                        $data[$signatureKey] = $filename;
                    }
                }
                // If no new signature and we want to keep the old one
                elseif (!$signatureData && session()->has('contract_step3.' . $signatureKey)) {
                    $data[$signatureKey] = session('contract_step3.' . $signatureKey);
                }
            }

            // Save to both session keys to ensure compatibility
            session(['step3_data' => $data]);
            session(['contract_step3' => $data]);

            \Log::info('Saved Step 3 data to session:', $data);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error saving Step 3 data:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the data.'
            ], 500);
        }
    }

    public function step4()
    {
        if (!session()->has('step3_data')) {
            return redirect()->route('contracts.create');
        }

        return view('admin.contracts.step4');
    }

    public function storeStep4(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:bank_transfer,check,cash',
            'bank_name' => 'required_if:payment_method,bank_transfer',
            'bank_account_name' => 'required_if:payment_method,bank_transfer',
            'bank_account_number' => 'required_if:payment_method,bank_transfer',
            'check_number' => 'required_if:payment_method,check',
            'check_date' => 'required_if:payment_method,check|date',
            'payment_schedule' => 'required|json'
        ]);

        session(['contract_step4' => $data]);

        // Create the contract
        $contract = $this->createContract();

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract created successfully.');
    }

    public function saveStep4(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'nullable|in:bank_transfer,check,cash',
            'bank_name' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'check_number' => 'nullable|string',
            'check_date' => 'nullable|date',
            'payment_schedule' => 'nullable|json'
        ]);

        session(['contract_step4' => $data]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create or update contractor party
            $contractor = Party::updateOrCreate(
                ['email' => session('contract_step1.contractor_email')],
                [
                    'name' => session('contract_step1.contractor_name'),
                    'company_name' => session('contract_step1.contractor_company'),
                    'phone' => session('contract_step1.contractor_phone'),
                    'street' => session('contract_step1.contractor_street'),
                    'barangay' => session('contract_step1.contractor_barangay'),
                    'city' => session('contract_step1.contractor_city'),
                    'state' => session('contract_step1.contractor_state'),
                    'postal' => session('contract_step1.contractor_postal'),
                    'entity_type' => 'contractor'
                ]
            );

            // Create or update client party
            $client = Party::updateOrCreate(
                ['email' => session('contract_step1.client_email')],
                [
                    'name' => session('contract_step1.client_name'),
                    'company_name' => session('contract_step1.client_company'),
                    'phone' => session('contract_step1.client_phone'),
                    'street' => session('contract_step1.client_street'),
                    'barangay' => session('contract_step1.client_barangay'),
                    'city' => session('contract_step1.client_city'),
                    'state' => session('contract_step1.client_state'),
                    'postal' => session('contract_step1.client_postal'),
                    'entity_type' => 'client'
                ]
            );

            // Create property
            $property = Property::create([
                'property_type' => session('contract_step1.property_type'),
                'street' => session('contract_step1.property_street'),
                'barangay' => session('contract_step1.property_barangay'),
                'city' => session('contract_step1.property_city'),
                'state' => session('contract_step1.property_state'),
                'postal' => session('contract_step1.property_postal')
            ]);

            // Create contract
            $contract = Contract::create([
                'contractor_id' => $contractor->id,
                'client_id' => $client->id,
                'property_id' => $property->id,
                'title' => 'Contract for ' . $client->name,
                'scope_of_work' => collect(session('contract_step2.rooms'))->first()['scope'] ? 
                    \App\Models\ScopeType::whereIn('id', collect(session('contract_step2.rooms'))->first()['scope'])
                        ->pluck('name')
                        ->implode(', ') : 
                    'General Construction Work',
                'scope_description' => 'Construction work as per agreed specifications',
                'start_date' => session('contract_step2.start_date'),
                'end_date' => session('contract_step2.end_date'),
                'total_amount' => session('contract_step2.grand_total'),
                'materials_cost' => session('contract_step2.total_materials'),
                'labor_cost' => session('contract_step2.total_labor'),
                'payment_terms' => session('contract_step3.payment_terms'),
                'payment_method' => session('contract_step4.payment_method', 'cash'),
                'bank_name' => session('contract_step4.bank_name'),
                'bank_account_name' => session('contract_step4.bank_account_name'),
                'bank_account_number' => session('contract_step4.bank_account_number'),
                'contractor_signature' => session('contract_step3.contractor_signature'),
                'client_signature' => session('contract_step3.client_signature'),
                'payment_schedule' => session('contract_step4.payment_schedule'),
                'status' => 'draft'
            ]);

            // Create rooms and their scopes
            $totalEstimatedDays = 0;
            foreach (session('contract_step2.rooms', []) as $roomId => $roomData) {
                // Calculate estimated days for this room
                $roomEstimatedDays = 0;
                if (!empty($roomData['scope'])) {
                    $roomEstimatedDays = (int)\App\Models\ScopeType::whereIn('id', $roomData['scope'])
                        ->sum('estimated_days');
                }

                $room = $contract->rooms()->create([
                    'name' => $roomData['name'],
                    'length' => $roomData['length'],
                    'width' => $roomData['width'],
                    'area' => $roomData['area'],
                    'materials_cost' => $roomData['materials_cost'] ?? 0,
                    'labor_cost' => $roomData['labor_cost'] ?? 0,
                    'estimated_days' => $roomEstimatedDays
                ]);

                if (!empty($roomData['scope'])) {
                    $room->scopeTypes()->attach($roomData['scope']);
                }

                // Update total estimated days (use max since some work can be done in parallel)
                $totalEstimatedDays = max($totalEstimatedDays, $roomEstimatedDays);
            }

            // Update contract end date and estimated days
            $contract->estimated_days = $totalEstimatedDays;
            $contract->end_date = \Carbon\Carbon::parse($contract->start_date)->addDays((int)$totalEstimatedDays);
            $contract->save();

            // Create contract items
            foreach ($contract->rooms as $room) {
                $room->load(['scopeTypes' => function($query) {
                    $query->with(['materials' => function($q) {
                        $q->with(['suppliers' => function($sq) {
                            $sq->wherePivot('is_preferred', true);
                        }]);
                    }]);
                }]);

                foreach ($room->scopeTypes as $scope) {
                    foreach ($scope->materials as $material) {
                        // Get the price from either srp_price or base_price
                        $price = floatval($material->srp_price ?? $material->base_price ?? 0);
                        $quantity = 1;
                        
                        // Calculate quantity based on area if needed
                        if ($material->is_per_area) {
                            $coverage = floatval($material->coverage_rate ?? 1);
                            $quantity = ceil($room->area / $coverage);
                        }

                        // Apply waste factor
                        $wasteFactor = floatval($material->waste_factor ?? 1.1);
                        $quantity = ceil($quantity * $wasteFactor);

                        // Check for bulk pricing
                        if ($material->bulk_pricing) {
                            $bulkPricing = is_array($material->bulk_pricing) ? $material->bulk_pricing : json_decode($material->bulk_pricing, true);
                            if (is_array($bulkPricing)) {
                                foreach ($bulkPricing as $tier) {
                                    if ($quantity >= ($tier['min_quantity'] ?? 0)) {
                                        $price = floatval($tier['price'] ?? $price);
                                    }
                                }
                            }
                        }

                        // Get preferred supplier if exists
                        $supplier = $material->suppliers->first();

                        // Create contract item
                        $contract->items()->create([
                            'material_id' => $material->id,
                            'material_name' => $material->name,
                            'unit' => $material->unit ?? 'pcs',
                            'supplier_id' => $supplier ? $supplier->id : null,
                            'supplier_name' => $supplier ? $supplier->company_name : null,
                            'quantity' => $quantity,
                            'amount' => $price,
                            'total' => $quantity * $price
                        ]);
                    }
                }
            }

            DB::commit();

            // Clear session data
            session()->forget(['contract_step1', 'contract_step2', 'contract_step3']);

            return redirect()->route('contracts.show', $contract->id)
                ->with('success', 'Contract created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Contract creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Contract creation failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Contract $contract)
    {
        // Load related data
        $contract->load(['contractor', 'client', 'property', 'rooms.scopeTypes']);

        // Step 1: Contractor, Client, Property
        $step1 = [
            'contractor_name' => $contract->contractor->name ?? '',
            'contractor_company' => $contract->contractor->company_name ?? '',
            'contractor_email' => $contract->contractor->email ?? '',
            'contractor_phone' => $contract->contractor->phone ?? '',
            'contractor_street' => $contract->contractor->street ?? '',
            'contractor_barangay' => $contract->contractor->barangay ?? '',
            'contractor_city' => $contract->contractor->city ?? '',
            'contractor_state' => $contract->contractor->state ?? '',
            'contractor_postal' => $contract->contractor->postal ?? '',
            'client_name' => $contract->client->name ?? '',
            'client_company' => $contract->client->company_name ?? '',
            'client_email' => $contract->client->email ?? '',
            'client_phone' => $contract->client->phone ?? '',
            'client_street' => $contract->client->street ?? '',
            'client_unit' => $contract->client->unit ?? '',
            'client_barangay' => $contract->client->barangay ?? '',
            'client_city' => $contract->client->city ?? '',
            'client_state' => $contract->client->state ?? '',
            'client_postal' => $contract->client->postal ?? '',
            'property_type' => $contract->property->property_type ?? '',
            'property_street' => $contract->property->street ?? '',
            'property_unit' => $contract->property->unit ?? '',
            'property_barangay' => $contract->property->barangay ?? '',
            'property_city' => $contract->property->city ?? '',
            'property_state' => $contract->property->state ?? '',
            'property_postal' => $contract->property->postal ?? '',
        ];
        session(['contract_step1' => $step1]);

        // Step 2: Rooms, Dates, Totals
        $rooms = [];
        foreach ($contract->rooms as $room) {
            $rooms[] = [
                'name' => $room->name,
                'length' => $room->length,
                'width' => $room->width,
                'area' => $room->area,
                'scope' => $room->scopeTypes->pluck('id')->toArray(),
                'materials_cost' => $room->materials_cost ?? 0,
                'labor_cost' => $room->labor_cost ?? 0,
            ];
        }
        $step2 = [
            'rooms' => $rooms,
            'start_date' => $contract->start_date ? $contract->start_date->format('Y-m-d') : '',
            'end_date' => $contract->end_date ? $contract->end_date->format('Y-m-d') : '',
            'total_materials' => $contract->materials_cost ?? 0,
            'total_labor' => $contract->labor_cost ?? 0,
            'grand_total' => $contract->total_amount ?? 0,
            'total_amount' => $contract->total_amount ?? 0,
            'labor_cost' => $contract->labor_cost ?? 0,
            'materials_cost' => $contract->materials_cost ?? 0,
        ];
        session(['contract_step2' => $step2]);

        // Step 3: Terms & Signatures
        $step3 = [
            'payment_terms' => $contract->payment_terms ?? '',
            'warranty_terms' => $contract->warranty_terms ?? '',
            'cancellation_terms' => $contract->cancellation_terms ?? '',
            'additional_terms' => $contract->additional_terms ?? '',
            'contractor_signature' => $contract->contractor_signature ?? '',
            'client_signature' => $contract->client_signature ?? '',
        ];
        session(['contract_step3' => $step3]);

        // Step 4: Payment details
        $step4 = [
            'payment_method' => $contract->payment_method ?? '',
            'bank_name' => $contract->bank_name ?? '',
            'bank_account_name' => $contract->bank_account_name ?? '',
            'bank_account_number' => $contract->bank_account_number ?? '',
            'check_number' => $contract->check_number ?? '',
            'check_date' => $contract->check_date ? $contract->check_date->format('Y-m-d') : '',
        ];
        session(['contract_step4' => $step4]);

        // Store contract ID to indicate edit mode
        session(['editing_contract_id' => $contract->id]);

        // Redirect to step1
        return redirect()->route('contracts.step1');
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
                            'supplier_name' => null,
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

    public function updateStatus(Request $request, Contract $contract)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,approved,rejected'
            ]);

            DB::beginTransaction();

            $contract->status = $request->status;
            $contract->save();

            // Generate payments when contract is approved
            if ($request->status === 'approved') {
                $contract->generatePayments();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contract status updated successfully',
                'status' => ucfirst($contract->status)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
        $contracts = Contract::with(['client', 'contractor'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('admin.contracts.timeline', compact('contracts'));
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
} 