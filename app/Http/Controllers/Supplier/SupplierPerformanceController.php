<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetrics;

class SupplierPerformanceController extends Controller
{
    public function index()
    {
        $supplier = Auth::user()->company;

        if (!$supplier || $supplier->designation !== 'supplier') {
            abort(403, 'You are not associated with a supplier account.');
        }

        $evaluations = SupplierEvaluation::where('supplier_id', $supplier->id)->latest()->paginate(10);
        $metrics = SupplierMetrics::where('supplier_id', $supplier->id)->first();

        return view('supplier.performance.index', compact('supplier', 'evaluations', 'metrics'));
    }
} 