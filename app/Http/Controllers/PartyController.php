<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');
        
        $clients = Party::where('type', 'client')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('company_name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->take(10)
            ->get();
            
        return response()->json($clients);
    }
} 