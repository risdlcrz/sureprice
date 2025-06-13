<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = \App\Models\Transaction::with('payment')->orderBy('date', 'desc')->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        $contracts = Contract::with(['transactions', 'purchaseOrders'])->get();
        $contract_id = $request->get('contract_id');
        return view('admin.transactions.create', compact('contracts', 'contract_id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'status' => 'required|string',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        Transaction::create($validated);
        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function edit(Transaction $transaction)
    {
        $contracts = Contract::all();
        return view('admin.transactions.edit', compact('transaction', 'contracts'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'status' => 'required|string',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $transaction->update($validated);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }
} 