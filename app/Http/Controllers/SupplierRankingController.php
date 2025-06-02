<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetric;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierRankingController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'final_score');
        $order = $request->get('order', 'desc');

        $validCategories = [
            'engagement_score' => 'Engagement',
            'delivery_speed_score' => 'Delivery Speed',
            'performance_score' => 'Performance',
            'quality_score' => 'Quality of Materials',
            'cost_variance_score' => 'Cost Variance',
            'sustainability_score' => 'Sustainability',
            'final_score' => 'Overall Score'
        ];

        if (!array_key_exists($category, $validCategories)) {
            $category = 'final_score';
        }

        // Get suppliers with their latest evaluations
        $suppliers = Supplier::with(['latestEvaluation', 'metrics'])
            ->get()
            ->map(function ($supplier) use ($category) {
                $data = $supplier->toArray();
                $data['rating'] = $supplier->latestEvaluation ? $supplier->latestEvaluation->$category : 0;
                return $data;
            });

        // Sort suppliers based on category and order
        $sortedSuppliers = $suppliers->sortBy(function ($supplier) use ($category, $order) {
            return $supplier['rating'];
        });

        if ($order === 'desc') {
            $sortedSuppliers = $sortedSuppliers->reverse();
        }

        // Add rank to each supplier
        $rankedSuppliers = $sortedSuppliers->map(function ($supplier, $index) {
            $supplier['rank'] = $index + 1;
            return $supplier;
        });

        return view('admin.supplier-rankings', [
            'suppliers' => $rankedSuppliers,
            'category' => $category,
            'order' => $order,
            'validCategories' => $validCategories
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create the supplier with only the fields that exist in the table
            $supplier = new Supplier();
            $supplier->fill([
                'company' => $request->company,
                'supplier_type' => $request->supplier_type,
                'business_reg_no' => $request->business_reg_no,
                'contact_person' => $request->contact_person,
                'designation' => $request->designation,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'telephone_number' => $request->telephone_number,
                'street' => $request->street,
                'city' => $request->city,
                'province' => $request->province,
                'zip_code' => $request->zip_code,
                'payment_terms' => $request->payment_terms,
                'vat_registered' => $request->has('vat_registered'),
                'use_sureprice' => $request->has('use_sureprice'),
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ]);
            $supplier->save();

            // Handle materials through the pivot table
            if ($request->has('materials')) {
                foreach ($request->materials as $material) {
                    $supplier->materials()->create([
                        'material_name' => $material
                    ]);
                }
            }

            // Handle file uploads
            $this->handleFileUploads($supplier, $request);

            DB::commit();

            return redirect()->route('supplier-rankings.index')
                ->with('success', 'Supplier added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('supplier-rankings.index')
                ->with('error', 'Failed to add supplier: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $supplier->fill($request->only([
                'company',
                'supplier_type',
                'business_reg_no',
                'contact_person',
                'designation',
                'email',
                'mobile_number',
                'telephone_number',
                'street',
                'city',
                'province',
                'zip_code',
                'payment_terms',
                'vat_registered',
                'use_sureprice',
                'bank_name',
                'account_name',
                'account_number'
            ]));
            $supplier->vat_registered = $request->input('vat_registered') === 'Yes';
            $supplier->use_sureprice = $request->input('use_sureprice') === 'Yes';
            $supplier->save();

            // Update materials
            $supplier->materials()->delete();
            if ($request->has('materials')) {
                foreach ($request->materials as $material) {
                    $supplier->materials()->create([
                        'material_name' => $material
                    ]);
                }
            }

            // Handle file uploads
            $this->handleFileUploads($supplier, $request);

            DB::commit();

            return redirect()->route('supplier-rankings.index')
                ->with('success', 'Supplier updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('supplier-rankings.index')
                ->with('error', 'Failed to update supplier: ' . $e->getMessage());
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            // Delete related records
            $supplier->evaluations()->delete();
            $supplier->metrics()->delete();
            $supplier->delete();

            DB::commit();

            return redirect()->route('supplier-rankings.index')
                ->with('success', 'Supplier deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('supplier-rankings.index')
                ->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['materials', 'latestEvaluation', 'evaluations']);
        return response()->json([
            'id' => $supplier->id,
            'company' => $supplier->company,
            'supplier_type' => $supplier->supplier_type,
            'business_reg_no' => $supplier->business_reg_no,
            'contact_person' => $supplier->contact_person,
            'designation' => $supplier->designation,
            'email' => $supplier->email,
            'mobile_number' => $supplier->mobile_number,
            'telephone_number' => $supplier->telephone_number,
            'street' => $supplier->street,
            'city' => $supplier->city,
            'province' => $supplier->province,
            'zip_code' => $supplier->zip_code,
            'payment_terms' => $supplier->payment_terms,
            'vat_registered' => $supplier->vat_registered,
            'use_sureprice' => $supplier->use_sureprice,
            'bank_name' => $supplier->bank_name,
            'account_name' => $supplier->account_name,
            'account_number' => $supplier->account_number,
            'materials' => $supplier->materials->map(function($m) { return ['material_name' => $m->material_name]; }),
            'evaluations' => $supplier->evaluations->map(function($evaluation) {
                return [
                    'evaluation_date' => $evaluation->evaluation_date,
                    'engagement_score' => $evaluation->engagement_score,
                    'delivery_speed_score' => $evaluation->delivery_speed_score,
                    'performance_score' => $evaluation->performance_score,
                    'quality_score' => $evaluation->quality_score,
                    'cost_variance_score' => $evaluation->cost_variance_score,
                    'sustainability_score' => $evaluation->sustainability_score,
                    'final_score' => $evaluation->final_score
                ];
            }),
            'dti_sec_registration' => $supplier->dti_sec_registration_path,
            'accreditation_docs' => $supplier->accreditation_docs_path,
            'mayors_permit' => $supplier->mayors_permit_path,
            'valid_id' => $supplier->valid_id_path,
            'company_profile' => $supplier->company_profile_path,
            'price_list' => $supplier->price_list_path
        ]);
    }

    public function evaluate(Request $request, Supplier $supplier)
    {
        try {
            $validated = $request->validate([
                'engagement_score' => 'required|numeric|min:0|max:5',
                'delivery_speed_score' => 'required|numeric|min:0|max:5',
                'performance_score' => 'required|numeric|min:0|max:5',
                'quality_score' => 'required|numeric|min:0|max:5',
                'cost_variance_score' => 'required|numeric|min:0|max:5',
                'sustainability_score' => 'required|numeric|min:0|max:5',
            ]);

            $evaluation = $supplier->evaluations()->create([
                'engagement_score' => $validated['engagement_score'],
                'delivery_speed_score' => $validated['delivery_speed_score'],
                'performance_score' => $validated['performance_score'],
                'quality_score' => $validated['quality_score'],
                'cost_variance_score' => $validated['cost_variance_score'],
                'sustainability_score' => $validated['sustainability_score'],
                'final_score' => (
                    $validated['engagement_score'] +
                    $validated['delivery_speed_score'] +
                    $validated['performance_score'] +
                    $validated['quality_score'] +
                    $validated['cost_variance_score'] +
                    $validated['sustainability_score']
                ) / 6,
                'evaluation_date' => now(),
            ]);

            return redirect()->route('supplier-rankings.index')
                ->with('evaluate_success', 'Supplier evaluation submitted successfully!')
                ->with('show_evaluate_modal', true);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors($request->validator)
                ->withInput()
                ->with('show_evaluate_modal', true);
        }
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load(['materials', 'evaluations']);
        return view('admin.supplier-edit', compact('supplier'));
    }

    private function handleFileUploads($supplier, $request)
    {
        $uploadDir = 'uploads/supplier_docs/';
        if (!file_exists(public_path($uploadDir))) {
            mkdir(public_path($uploadDir), 0777, true);
        }

        $fileFields = [
            'dti_sec_registration',
            'accreditation_docs',
            'mayors_permit',
            'valid_id',
            'company_profile',
            'price_list'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = $supplier->id . '_' . $field . '_' . $file->getClientOriginalName();
                $file->move(public_path($uploadDir), $fileName);
                $supplier->{$field . '_path'} = $uploadDir . $fileName;
            }
        }

        $supplier->save();
    }
} 