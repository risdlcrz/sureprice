<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvitation;
use App\Models\Contract;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupplierInvitationMail;
use App\Models\Category;

class SupplierInvitationController extends Controller
{
    public function index()
    {
        $invitations = SupplierInvitation::with(['contract', 'materials'])
            ->latest()
            ->paginate(10);

        return view('admin.supplier-invitations.index', compact('invitations'));
    }

    public function create()
    {
        $contracts = Contract::with(['client', 'contractor'])
            ->where('status', 'approved')
            ->orderBy('contract_number')
            ->get();
        $materials = Material::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        return view('admin.supplier-invitations.form', compact('contracts', 'materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'materials' => 'required|array|min:1',
            'materials.*' => 'exists:materials,id',
            'message' => 'nullable|string',
            'due_date' => 'required|date|after:today'
        ]);

        $invitation = SupplierInvitation::create([
            'contract_id' => $validated['contract_id'],
            'invitation_code' => 'INV-' . Str::random(8),
            'company_name' => $validated['company_name'],
            'contact_name' => $validated['contact_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'due_date' => $validated['due_date'],
            'status' => 'pending'
        ]);

        $invitation->materials()->attach($validated['materials']);

        // Send invitation email
        Mail::to($validated['email'])->send(new SupplierInvitationMail($invitation));

        return redirect()->route('supplier-invitations.show', $invitation)
            ->with('success', 'Supplier invitation created and sent successfully.');
    }

    public function show(SupplierInvitation $invitation)
    {
        $invitation->load(['contract', 'materials']);
        return view('admin.supplier-invitations.show', compact('invitation'));
    }

    public function edit(SupplierInvitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('supplier-invitations.show', $invitation)
                ->with('error', 'This invitation cannot be edited.');
        }

        $contracts = Contract::where('status', 'approved')
            ->orderBy('contract_number')
            ->get();
        $materials = Material::all();
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();
        return view('admin.supplier-invitations.form', compact('invitation', 'contracts', 'materials', 'suppliers'));
    }

    public function update(Request $request, SupplierInvitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('supplier-invitations.show', $invitation)
                ->with('error', 'This invitation cannot be updated.');
        }

        $validated = $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'materials' => 'required|array|min:1',
            'materials.*' => 'exists:materials,id',
            'message' => 'nullable|string',
            'due_date' => 'required|date|after:today'
        ]);

        $invitation->update([
            'contract_id' => $validated['contract_id'],
            'company_name' => $validated['company_name'],
            'contact_name' => $validated['contact_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'due_date' => $validated['due_date']
        ]);

        $invitation->materials()->sync($validated['materials']);

        return redirect()->route('supplier-invitations.show', $invitation)
            ->with('success', 'Supplier invitation updated successfully.');
    }

    public function destroy(SupplierInvitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('supplier-invitations.show', $invitation)
                ->with('error', 'This invitation cannot be deleted.');
        }

        $invitation->materials()->detach();
        $invitation->delete();

        return redirect()->route('supplier-invitations.index')
            ->with('success', 'Supplier invitation deleted successfully.');
    }

    public function resend(SupplierInvitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('supplier-invitations.show', $invitation)
                ->with('error', 'This invitation cannot be resent.');
        }

        // Resend invitation email
        Mail::to($invitation->email)->send(new SupplierInvitationMail($invitation));

        return redirect()->route('supplier-invitations.show', $invitation)
            ->with('success', 'Invitation resent successfully.');
    }

    public function showResponse($code)
    {
        $invitation = SupplierInvitation::where('invitation_code', $code)
            ->where('status', 'pending')
            ->firstOrFail();

        return view('supplier.respond', compact('invitation'));
    }

    public function processResponse(Request $request, $code)
    {
        $invitation = SupplierInvitation::where('invitation_code', $code)
            ->where('status', 'pending')
            ->firstOrFail();

        $validated = $request->validate([
            'response' => 'required|in:accept,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        $invitation->update([
            'status' => $validated['response'] === 'accept' ? 'accepted' : 'rejected',
            'response_notes' => $validated['notes'],
            'responded_at' => now(),
        ]);

        return redirect()->route('supplier.respond', $code)
            ->with('success', 'Thank you for your response.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $contractId = $request->get('contract_id');
        
        $invitations = SupplierInvitation::with(['contract', 'materials'])
            ->when($query, function($q) use ($query) {
                return $q->where('invitation_code', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->when($contractId, function($q) use ($contractId) {
                return $q->where('contract_id', $contractId);
            })
            ->latest()
            ->get();
            
        return response()->json($invitations);
    }
} 