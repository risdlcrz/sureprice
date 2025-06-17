<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProcurementSupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%');
            });
        }

        $suppliers = $query->latest()->paginate(10);

        return view('procurement.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('procurement.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user account for the supplier
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'supplier',
        ]);

        // Create supplier record
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'contact_person' => $validated['contact_person'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('procurement.suppliers.show', $supplier)->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('user');
        return view('procurement.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('user');
        return view('procurement.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($supplier->user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user account
        $supplier->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $supplier->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Update supplier record
        $supplier->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'contact_person' => $validated['contact_person'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('procurement.suppliers.show', $supplier)->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // Also delete the associated user account
        $supplier->user->delete();
        $supplier->delete();

        return redirect()->route('procurement.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
} 