<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
} 