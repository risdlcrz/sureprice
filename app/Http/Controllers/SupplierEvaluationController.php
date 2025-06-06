<?php

namespace App\Http\Controllers;

use App\Models\SupplierEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierEvaluationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date',
            'ratings' => 'required|array',
            'ratings.*' => 'required|numeric|min:0.5|max:5',
            'comments' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $evaluation = SupplierEvaluation::create([
                'supplier_id' => $validated['supplier_id'],
                'evaluator_id' => $validated['evaluator_id'],
                'evaluation_date' => $validated['evaluation_date'],
                'ratings' => $validated['ratings'],
                'comments' => $validated['comments'],
                'average_rating' => collect($validated['ratings'])->avg()
            ]);

            DB::commit();

            return redirect()->route('suppliers.show', $validated['supplier_id'])
                ->with('success', 'Supplier evaluation submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit evaluation. Please try again.');
        }
    }
} 