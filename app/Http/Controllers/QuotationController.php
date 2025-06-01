<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Project;
use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with(['project', 'suppliers', 'materials'])
            ->latest()
            ->paginate(10);

        $projects = Project::all();
        return view('admin.quotations.index', compact('quotations', 'projects'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('admin.quotations.form', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'due_date' => 'required|date|after:today',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_notes' => 'array',
            'supplier_notes.*' => 'nullable|string',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.specifications' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $quotation = Quotation::create([
            'project_id' => $validated['project_id'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'status' => 'draft',
            'rfq_number' => 'RFQ-' . Str::random(8)
        ]);

        // Attach suppliers with notes
        foreach ($validated['suppliers'] as $supplierId) {
            $quotation->suppliers()->attach($supplierId, [
                'notes' => $validated['supplier_notes'][$supplierId] ?? null
            ]);
        }

        // Attach materials with specifications
        foreach ($validated['materials'] as $materialData) {
            $quotation->materials()->attach($materialData['id'], [
                'quantity' => $materialData['quantity'],
                'specifications' => $materialData['specifications'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotations/' . $quotation->id);
                $quotation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('quotations.index')
            ->with('success', 'RFQ created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['project', 'suppliers', 'materials', 'attachments']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This RFQ cannot be edited.');
        }

        $projects = Project::all();
        $quotation->load(['project', 'suppliers', 'materials', 'attachments']);
        return view('admin.quotations.form', compact('quotation', 'projects'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This RFQ cannot be updated.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'due_date' => 'required|date|after:today',
            'suppliers' => 'required|array',
            'suppliers.*' => 'exists:suppliers,id',
            'supplier_notes' => 'array',
            'supplier_notes.*' => 'nullable|string',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.specifications' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $quotation->update([
            'project_id' => $validated['project_id'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes']
        ]);

        // Sync suppliers with notes
        $supplierSync = [];
        foreach ($validated['suppliers'] as $supplierId) {
            $supplierSync[$supplierId] = [
                'notes' => $validated['supplier_notes'][$supplierId] ?? null
            ];
        }
        $quotation->suppliers()->sync($supplierSync);

        // Sync materials with specifications
        $materialSync = [];
        foreach ($validated['materials'] as $materialData) {
            $materialSync[$materialData['id']] = [
                'quantity' => $materialData['quantity'],
                'specifications' => $materialData['specifications'] ?? null
            ];
        }
        $quotation->materials()->sync($materialSync);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotations/' . $quotation->id);
                $quotation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('quotations.index')
            ->with('success', 'RFQ updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        // Delete attachments from storage
        foreach ($quotation->attachments as $attachment) {
            Storage::delete($attachment->path);
        }

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'RFQ deleted successfully.');
    }

    public function send(Quotation $quotation)
    {
        if ($quotation->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft RFQs can be sent.'
            ]);
        }

        // Here you would typically send emails to suppliers
        // For now, we'll just update the status
        $quotation->update(['status' => 'sent']);

        return response()->json(['success' => true]);
    }

    public function approve(Quotation $quotation)
    {
        if ($quotation->status !== 'responded') {
            return response()->json([
                'success' => false,
                'message' => 'Only responded RFQs can be approved.'
            ]);
        }

        $quotation->update(['status' => 'approved']);

        return response()->json(['success' => true]);
    }

    public function reject(Quotation $quotation)
    {
        if ($quotation->status !== 'responded') {
            return response()->json([
                'success' => false,
                'message' => 'Only responded RFQs can be rejected.'
            ]);
        }

        $quotation->update(['status' => 'rejected']);

        return response()->json(['success' => true]);
    }

    public function removeAttachment(Request $request)
    {
        $validated = $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'attachment_id' => 'required|exists:attachments,id'
        ]);

        $quotation = Quotation::findOrFail($validated['quotation_id']);
        $attachment = $quotation->attachments()->findOrFail($validated['attachment_id']);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $materials = Material::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($materials);
    }
} 