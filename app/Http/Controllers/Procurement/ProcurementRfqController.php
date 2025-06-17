<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProcurementRfqController extends Controller
{
    public function index()
    {
        $rfqs = Rfq::withCount('responses')
            ->latest()
            ->paginate(10);

        return view('procurement.rfqs.index', compact('rfqs'));
    }

    public function create()
    {
        $materials = Material::all();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('procurement.rfqs.create', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
        ]);

        $rfq = Rfq::create([
            'rfq_number' => 'RFQ-' . Str::random(8),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        // Attach materials
        foreach ($validated['materials'] as $material) {
            $rfq->materials()->attach($material['material_id'], [
                'quantity' => $material['quantity']
            ]);
        }

        // Send invitations to suppliers
        foreach ($validated['suppliers'] as $supplierId) {
            $rfq->suppliers()->attach($supplierId, [
                'status' => 'pending',
                'invited_at' => now(),
            ]);
        }

        return redirect()
            ->route('procurement.rfqs.show', $rfq)
            ->with('success', 'RFQ created successfully.');
    }

    public function show(Rfq $rfq)
    {
        $rfq->load(['materials', 'suppliers', 'responses']);
        
        return view('procurement.rfqs.show', compact('rfq'));
    }

    public function edit(Rfq $rfq)
    {
        if ($rfq->status !== 'active') {
            return redirect()
                ->route('procurement.rfqs.show', $rfq)
                ->with('error', 'Only active RFQs can be edited.');
        }

        $materials = Material::all();
        $suppliers = Supplier::where('status', 'active')->get();
        $rfq->load(['materials', 'suppliers']);

        return view('procurement.rfqs.edit', compact('rfq', 'materials', 'suppliers'));
    }

    public function update(Request $request, Rfq $rfq)
    {
        if ($rfq->status !== 'active') {
            return redirect()
                ->route('procurement.rfqs.show', $rfq)
                ->with('error', 'Only active RFQs can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
        ]);

        $rfq->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
        ]);

        // Sync materials
        $materials = collect($validated['materials'])->mapWithKeys(function ($item) {
            return [$item['material_id'] => ['quantity' => $item['quantity']]];
        });
        $rfq->materials()->sync($materials);

        // Sync suppliers
        $suppliers = collect($validated['suppliers'])->mapWithKeys(function ($supplierId) {
            return [$supplierId => [
                'status' => 'pending',
                'invited_at' => now(),
            ]];
        });
        $rfq->suppliers()->sync($suppliers);

        return redirect()
            ->route('procurement.rfqs.show', $rfq)
            ->with('success', 'RFQ updated successfully.');
    }

    public function destroy(Rfq $rfq)
    {
        if ($rfq->status !== 'active') {
            return redirect()
                ->route('procurement.rfqs.show', $rfq)
                ->with('error', 'Only active RFQs can be deleted.');
        }

        $rfq->delete();

        return redirect()
            ->route('procurement.rfqs.index')
            ->with('success', 'RFQ deleted successfully.');
    }
} 