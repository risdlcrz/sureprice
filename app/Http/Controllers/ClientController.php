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
     * Get all clients for dropdown or list
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $clients = Party::clients()
            ->select('id', 'name', 'company_name', 'email', 'phone')
            ->orderBy('name')
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
     * Show a specific client
     *
     * @param Party $client
     * @return JsonResponse
     */
    public function show(Party $client): JsonResponse
    {
        // Ensure the party is actually a client
        if ($client->entity_type !== 'client') {
            return response()->json(['error' => 'Party is not a client'], 404);
        }

        return response()->json([
            'id' => $client->id,
            'name' => $client->name,
            'company_name' => $client->company_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'street' => $client->street,
            'barangay' => $client->barangay,
            'city' => $client->city,
            'state' => $client->state,
            'postal' => $client->postal
        ]);
    }

    /**
     * Show the client dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        Log::info('Client dashboard accessed', [
            'user_id' => auth()->id(),
            'authenticated' => auth()->check()
        ]);
        
        $user = auth()->user();
        $company = $user->company;
        
        Log::info('Client dashboard - user and company info', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'company_exists' => $company ? true : false,
            'company_designation' => $company ? $company->designation : null,
            'company_status' => $company ? $company->status : null
        ]);
        
        if (!$company) {
            Log::info('No company associated with user, redirecting to login');
            return redirect()->route('login.form')->with('error', 'No company associated with this account.');
        }

        // Find the client party record for this user
        $clientParty = \App\Models\Party::where('user_id', $user->id)
            ->where('entity_type', 'client')
            ->first();

        Log::info('Client party lookup', [
            'user_id' => $user->id,
            'client_party_exists' => $clientParty ? true : false,
            'client_party_id' => $clientParty ? $clientParty->id : null
        ]);

        if (!$clientParty) {
            Log::info('No client profile found, redirecting to login');
            return redirect()->route('login.form')->with('error', 'No client profile found. Please contact the administrator.');
        }

        // Get only the client's own contracts using the party relationship
        $contracts = Contract::where('client_id', $clientParty->id)
            ->with(['items'])
            ->latest()
            ->get();

        Log::info('Client dashboard rendering', [
            'contracts_count' => $contracts->count()
        ]);

        return view('client.dashboard', compact('user', 'company', 'contracts'));
    }

    /**
     * Show the Project & Procurement module for the client (read-only, no approval actions)
     */
    public function projectProcurement()
    {
        $user = auth()->user();
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