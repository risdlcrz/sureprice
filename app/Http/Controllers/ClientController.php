<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Search for clients by name or company name
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = Party::clients()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'company_name', 'email', 'phone')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->name . ($client->company_name ? ' (' . $client->company_name . ')' : ''),
                    'name' => $client->name,
                    'company_name' => $client->company_name,
                    'email' => $client->email,
                    'phone' => $client->phone
                ];
            });

        return response()->json($clients);
    }

    /**
     * Show the client dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('login.form')->with('error', 'No company associated with this account.');
        }

        // Only the client's own records (none yet is normal for a new client — the page shows empty states)
        $contracts = Contract::visibleTo($user)->latest()->get();
        $quotationRequests = \App\Models\QuotationRequest::where('user_id', $user->id)->withCount('rooms')->latest()->get();
        $payments = \App\Models\Payment::with('contract')
            ->whereHas('contract', fn ($q) => $q->visibleTo($user))
            ->orderBy('due_date')
            ->get();
        $openPayments = $payments->whereNotIn('status', ['paid', 'cancelled'])->values();
        $unreadMessages = \App\Support\UnreadCounts::messages();

        return view('client.dashboard', compact('user', 'company', 'contracts', 'quotationRequests', 'payments', 'openPayments', 'unreadMessages'));
    }

    /**
     * Show the Project & Procurement module for the client (read-only, no approval actions)
     */
    public function projectProcurement()
    {
        $user = auth()->user();
        $user->loadMissing('company');
        $company = $user->company;
        if (!$company) {
            return redirect()->route('client.dashboard')->with('error', 'No company associated with this account.');
        }

        $contracts = Contract::visibleTo($user)
            ->with(['items'])
            ->latest()
            ->get();
        return view('client.project-procurement', compact('contracts'));
    }
} 