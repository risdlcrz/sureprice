<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        return Material::where('name', 'like', "%{$query}%")
            ->with(['suppliers' => function($q) {
                $q->where('is_preferred', true);
            }])
            ->take(10)
            ->get();
    }

    public function suppliers(Material $material)
    {
        return $material->suppliers()
            ->where('is_preferred', true)
            ->with('pivot')
            ->get();
    }
} 