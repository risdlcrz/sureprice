<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');
        
        $materials = Material::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with(['suppliers' => function($q) {
                $q->where('is_preferred', true);
            }])
            ->take(10)
            ->get();
            
        return response()->json($materials);
    }

    public function getSuppliers(Request $request, Material $material): JsonResponse
    {
        $suppliers = $material->suppliers()
            ->when($request->preferred, function($query) {
                return $query->where('is_preferred', true);
            })
            ->get();
            
        return response()->json($suppliers);
    }
} 