<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceAnalysisController extends Controller
{
    public function priceHistory($materialId, $supplierId = null)
    {
        $query = DB::table('material_supplier_price_histories')
            ->where('material_id', $materialId);
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        $history = $query->orderBy('date')->get();
        return response()->json($history);
    }
} 