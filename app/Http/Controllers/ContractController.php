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
            'items' => $contract->items
        ]);
    }

    public function update(Request $request, Contract $contract)
    {
        return $this->saveContract($request, $contract);
    }

    protected function saveContract(Request $request, Contract $contract = null)
    {
        try {
            DB::beginTransaction();

            // Save contractor
            $contractor = $this->saveParty($request, 'contractor');
            
            // Save client
            $client = $this->saveParty($request, 'client');
            
            // Save property
            $property = $this->saveProperty($request);
            
            // Process signatures
            $signatures = $this->handleSignatures($request);
            
            // Save contract
            $contractData = [
                'contractor_id' => $contractor->id,
                'client_id' => $client->id,
                'property_id' => $property->id,
                'scope_of_work' => implode(', ', $request->input('scope_of_work', [])),
                'scope_description' => $request->input('scope_description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'total_amount' => $request->input('total_amount'),
                'jurisdiction' => $request->input('jurisdiction'),
                'contract_terms' => $request->input('contract_paragraphs'),
                'client_signature' => $signatures['client'],
                'contractor_signature' => $signatures['contractor'],
                'status' => 'draft'
            ];

            if ($contract) {
                $contract->update($contractData);
            } else {
                $contract = Contract::create($contractData);
            }

            // Save items
            $this->saveItems($request, $contract);

            DB::commit();

            return redirect()->route('contracts.show', $contract->id)
                ->with('success', 'Contract saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving contract: ' . $e->getMessage())
                ->withInput();
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
            'client' => $request->input('existing_client_signature', ''),
            'contractor' => $request->input('existing_contractor_signature', '')
        ];

        foreach (['client', 'contractor'] as $type) {
            // Handle file uploads
            if ($request->hasFile("{$type}_signature")) {
                $path = $request->file("{$type}_signature")->store('signatures', 'public');
                $signatures[$type] = Storage::url($path);
            }
            
            // Handle canvas signatures
            if ($request->filled("{$type}_signature_data")) {
                $image_data = base64_decode(explode(',', $request->input("{$type}_signature_data"))[1]);
                $filename = "{$type}_signature_" . time() . '.png';
                $path = "signatures/{$filename}";
                
                Storage::disk('public')->put($path, $image_data);
                $signatures[$type] = Storage::url($path);
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