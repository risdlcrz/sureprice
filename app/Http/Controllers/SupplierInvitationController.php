<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvitation;
use App\Models\Contract;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupplierInvitationMail;

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
        $validated = $this->validateInvitation($request);

        $invitation = SupplierInvitation::create(Arr::except($validated, 'materials') + [
            'invitation_code' => 'INV-' . Str::upper(Str::random(12)),
            'status' => 'pending',
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

        $validated = $this->validateInvitation($request);

        $invitation->update(Arr::except($validated, 'materials'));

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

    /** Public page the invitation email links to (no login needed; the code is the credential). */
    public function showResponse(string $code)
    {
        $invitation = SupplierInvitation::with(['contract', 'materials'])
            ->where('invitation_code', $code)
            ->firstOrFail();

        return view('supplier.respond', compact('invitation'));
    }

    public function processResponse(Request $request, string $code)
    {
        $invitation = SupplierInvitation::where('invitation_code', $code)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('supplier.respond', $code)
                ->with('info', 'You have already responded to this invitation.');
        }

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

    private function validateInvitation(Request $request): array
    {
        return $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'materials' => 'required|array|min:1',
            'materials.*' => 'exists:materials,id',
            'message' => 'nullable|string',
            'due_date' => 'required|date|after:today',
        ]) + ['contract_id' => null, 'message' => null];
    }
}
