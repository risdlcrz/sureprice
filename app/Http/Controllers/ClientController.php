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

        // Find the client party record for this user (middleware already confirmed client designation)
        $user->loadMissing('company');
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        if (!$clientParty) {
            return redirect()->route('landing.catalogue')->with('error', 'No client profile found. Please contact the administrator.');
        }

        // Get only the client's own contracts using the party relationship
        $contracts = Contract::where('client_id', $clientParty->id)
            ->with(['items'])
            ->latest()
            ->get();

        return view('client.dashboard', compact('user', 'company', 'contracts'));
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

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        if (!$clientParty) {
            return redirect()->route('client.dashboard')->with('error', 'No client profile found. Please contact the administrator.');
        }

        $contracts = Contract::where('client_id', $clientParty->id)
            ->with(['items'])
            ->latest()
            ->get();
        return view('client.project-procurement', compact('contracts'));
    }
} 