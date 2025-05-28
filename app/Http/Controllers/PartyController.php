<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        return Party::where('type', 'client')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'company_name', 'entity_type', 'email', 'phone', 'street', 'city', 'state', 'postal')
            ->limit(10)
            ->get();
    }
} 