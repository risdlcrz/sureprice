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
        $query = Transaction::with(['payment', 'contract', 'creator']);

        // If user is not admin, only show their transactions
        if (auth()->user()->user_type !== 'admin') {
            $query->whereHas('contract', function($q) {
                $q->where('client_id', auth()->user()->party->id);
            });
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(15);
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

    public function pastTransactions(Request $request)
    {
        dd('You are in the pastTransactions method!');
        // Fetch all purchase request items (could also include purchase orders, etc.)
        $items = \App\Models\PurchaseRequestItem::with(['material', 'supplier'])
            ->orderByDesc('created_at')
            ->get();

        // Frequency by material/service
        $frequency = $items->groupBy('material_id')->map(function($group) {
            return [
                'material' => $group->first()->material->name ?? 'Unknown',
                'count' => $group->count()
            ];
        })->values();

        // Monthly forecast (count per month)
        $monthly = $items->groupBy(function($item) {
            return $item->created_at->format('Y-m');
        })->map(function($group, $month) {
            return [
                'month' => $month,
                'count' => $group->count()
            ];
        })->sortBy('month')->values();

        return view('transactions.past', compact('items', 'frequency', 'monthly'));
    }
} 