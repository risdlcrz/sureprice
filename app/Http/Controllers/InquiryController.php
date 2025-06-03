<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Contract;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InquiryController extends Controller
{
    public function index()
    {
        $query = Inquiry::with(['contract', 'materials']);

        // Search filter
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('contract', function($q) use ($search) {
                      $q->where('contract_id', 'like', "%{$search}%");
                  });
            });
        }

        // Priority filter
        if ($priority = request('priority')) {
            $query->where('priority', $priority);
        }

        // Status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Per page filter
        $perPage = request('per_page', 10);
        $inquiries = $query->latest()->paginate($perPage);
            
        $contracts = Contract::where('status', 'approved')
            ->orderBy('contract_id')
            ->get();

        return view('admin.inquiries.index', compact('inquiries', 'contracts'));
    }

    public function create()
    {
        $contracts = Contract::with(['client', 'contractor'])
            ->where('status', 'approved')
            ->orderBy('contract_id')
            ->get();
        $materials = Material::orderBy('name')->get();
        return view('admin.inquiries.form', compact('contracts', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'required|date|after:today',
            'department' => 'required|string|max:255',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $inquiry = Inquiry::create([
            'contract_id' => $validated['contract_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'required_date' => $validated['required_date'],
            'department' => $validated['department'],
            'status' => 'pending'
        ]);

        foreach ($validated['materials'] as $materialData) {
            $inquiry->materials()->attach($materialData['id'], [
                'quantity' => $materialData['quantity'],
                'notes' => $materialData['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inquiries/' . $inquiry->id);
                $inquiry->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry created successfully.');
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['contract', 'materials', 'attachments']);
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function edit(Inquiry $inquiry)
    {
        $contracts = Contract::where('status', 'approved')
            ->orderBy('contract_id')
            ->get();
        $inquiry->load(['contract', 'materials', 'attachments']);
        return view('admin.inquiries.form', compact('inquiry', 'contracts'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'required|date|after:today',
            'department' => 'required|string|max:255',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $inquiry->update([
            'contract_id' => $validated['contract_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'required_date' => $validated['required_date'],
            'department' => $validated['department']
        ]);

        // Sync materials
        $inquiry->materials()->detach();
        foreach ($validated['materials'] as $materialData) {
            $inquiry->materials()->attach($materialData['id'], [
                'quantity' => $materialData['quantity'],
                'notes' => $materialData['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inquiries/' . $inquiry->id);
                $inquiry->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry updated successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        // Delete attachments from storage
        foreach ($inquiry->attachments as $attachment) {
            Storage::delete($attachment->path);
        }

        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }

    public function removeAttachment(Request $request)
    {
        $validated = $request->validate([
            'inquiry_id' => 'required|exists:inquiries,id',
            'attachment_id' => 'required|exists:attachments,id'
        ]);

        $inquiry = Inquiry::findOrFail($validated['inquiry_id']);
        $attachment = $inquiry->attachments()->findOrFail($validated['attachment_id']);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $contractId = $request->get('contract_id');
        
        $inquiries = Inquiry::with(['contract', 'materials'])
            ->when($query, function($q) use ($query) {
                return $q->where('subject', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->when($contractId, function($q) use ($contractId) {
                return $q->where('contract_id', $contractId);
            })
            ->latest()
            ->get();
            
        return response()->json($inquiries);
    }
} 