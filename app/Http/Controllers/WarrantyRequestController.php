<?php

namespace App\Http\Controllers;

use App\Models\WarrantyRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WarrantyRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'product_name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'receipt_number' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'issue_description' => 'required|string',
            'purchaseProof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'issuePhotos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if contract is completed
        $contract = Contract::findOrFail($request->contract_id);
        if ($contract->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Warranty requests can only be submitted for completed contracts.'
            ], 422);
        }

        try {
            // Store proof of purchase
            $proofPath = $request->file('purchaseProof')->store('warranty-proofs', 'public');

            // Store issue photos if any
            $issuePhotosPaths = [];
            if ($request->hasFile('issuePhotos')) {
                foreach ($request->file('issuePhotos') as $photo) {
                    $issuePhotosPaths[] = $photo->store('warranty-photos', 'public');
                }
            }

            // Create warranty request
            $warrantyRequest = WarrantyRequest::create([
                'contract_id' => $request->contract_id,
                'product_name' => $request->product_name,
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'receipt_number' => $request->receipt_number,
                'model_number' => $request->model_number,
                'issue_description' => $request->issue_description,
                'proof_of_purchase_path' => $proofPath,
                'issue_photos_paths' => $issuePhotosPaths,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Warranty request submitted successfully',
                'data' => $warrantyRequest
            ]);

        } catch (\Exception $e) {
            // Clean up uploaded files if something goes wrong
            if (isset($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }
            foreach ($issuePhotosPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit warranty request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $warrantyRequests = WarrantyRequest::with('contract')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('warranty-requests.index', compact('warrantyRequests'));
    }

    public function show(WarrantyRequest $warrantyRequest)
    {
        return view('warranty-requests.show', compact('warrantyRequest'));
    }

    public function updateStatus(Request $request, WarrantyRequest $warrantyRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_review,approved,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $warrantyRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warranty request status updated successfully',
            'data' => $warrantyRequest
        ]);
    }

    public function export(Request $request)
    {
        try {
            $query = WarrantyRequest::with(['contract.client']);

            // Apply filters if any
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                      ->orWhere('serial_number', 'like', "%{$search}%")
                      ->orWhere('issue_description', 'like', "%{$search}%")
                      ->orWhereHas('contract', function($q) use ($search) {
                          $q->where('contract_number', 'like', "%{$search}%")
                            ->orWhereHas('client', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                      });
                });
            }

            $warrantyRequests = $query->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="warranty-requests-' . date('Y-m-d') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($warrantyRequests) {
                $file = fopen('php://output', 'w');
                
                // Add headers
                fputcsv($file, [
                    'ID',
                    'Contract Number',
                    'Client Name',
                    'Product Name',
                    'Serial Number',
                    'Model Number',
                    'Purchase Date',
                    'Receipt Number',
                    'Issue Description',
                    'Status',
                    'Submitted Date',
                    'Reviewed Date',
                    'Admin Notes'
                ]);

                // Add data
                foreach ($warrantyRequests as $request) {
                    fputcsv($file, [
                        $request->id,
                        $request->contract->contract_number ?? 'N/A',
                        $request->contract->client->name ?? 'N/A',
                        $request->product_name,
                        $request->serial_number,
                        $request->model_number ?? 'N/A',
                        $request->purchase_date ?? 'N/A',
                        $request->receipt_number ?? 'N/A',
                        $request->issue_description,
                        ucfirst($request->status),
                        $request->created_at->format('Y-m-d H:i:s'),
                        $request->reviewed_at ? $request->reviewed_at->format('Y-m-d H:i:s') : 'N/A',
                        $request->admin_notes ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export warranty requests: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="warranty-requests-template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add headers with required field indicators
            fputcsv($file, [
                'Contract Number*',
                'Product Name*',
                'Serial Number*',
                'Model Number',
                'Purchase Date (YYYY-MM-DD)',
                'Receipt Number',
                'Issue Description*',
                'Status* (pending/in_review/approved/rejected)'
            ]);

            // Add example row
            fputcsv($file, [
                'CONTRACT-001',
                'Sample Product',
                'SN123456',
                'MODEL-789',
                '2024-03-21',
                'REC-001',
                'Product not working properly',
                'pending'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv|max:5120' // 5MB max
        ]);

        try {
            $file = fopen($request->file('file')->getPathname(), 'r');
            
            // Skip header row
            fgetcsv($file);
            
            $imported = 0;
            $errors = [];
            $row = 2; // Start from row 2 (after header)

            while (($data = fgetcsv($file)) !== false) {
                try {
                    // Validate required fields
                    if (count($data) < 8) {
                        throw new \Exception('Invalid number of columns');
                    }

                    // Find contract by number
                    $contract = Contract::where('contract_number', $data[0])->first();
                    if (!$contract) {
                        throw new \Exception('Contract not found');
                    }

                    // Validate status
                    $validStatuses = ['pending', 'in_review', 'approved', 'rejected'];
                    if (!in_array(strtolower($data[7]), $validStatuses)) {
                        throw new \Exception('Invalid status');
                    }

                    // Create warranty request
                    WarrantyRequest::create([
                        'contract_id' => $contract->id,
                        'product_name' => $data[1],
                        'serial_number' => $data[2],
                        'model_number' => $data[3] ?: null,
                        'purchase_date' => $data[4] ?: null,
                        'receipt_number' => $data[5] ?: null,
                        'issue_description' => $data[6],
                        'status' => strtolower($data[7]),
                        'proof_of_purchase_path' => null, // These will need to be uploaded separately
                        'issue_photos_paths' => []
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: " . $e->getMessage();
                }
                $row++;
            }

            fclose($file);

            if (count($errors) > 0) {
                return back()->with('warning', "Imported {$imported} records with " . count($errors) . " errors: " . implode(', ', $errors));
            }

            return back()->with('success', "Successfully imported {$imported} warranty requests");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import file: ' . $e->getMessage());
        }
    }

    public function storeAdditionalWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'work_type' => 'required|in:installation,maintenance,repair,upgrade,other',
            'description' => 'required|string',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.notes' => 'nullable|string',
            'estimated_hours' => 'required|numeric|min:0.5',
            'required_skills' => 'nullable|string',
            'labor_notes' => 'nullable|string',
            'preferred_start_date' => 'required|date|after_or_equal:today',
            'preferred_end_date' => 'required|date|after_or_equal:preferred_start_date',
            'timeline_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create additional work request
            $additionalWork = \App\Models\AdditionalWork::create([
                'contract_id' => $request->contract_id,
                'work_type' => $request->work_type,
                'description' => $request->description,
                'estimated_hours' => $request->estimated_hours,
                'required_skills' => $request->required_skills,
                'labor_notes' => $request->labor_notes,
                'preferred_start_date' => $request->preferred_start_date,
                'preferred_end_date' => $request->preferred_end_date,
                'timeline_notes' => $request->timeline_notes,
                'additional_notes' => $request->additional_notes,
                'status' => 'pending'
            ]);

            // Create material requirements
            foreach ($request->materials as $material) {
                $additionalWork->materials()->create([
                    'material_id' => $material['material_id'],
                    'quantity' => $material['quantity'],
                    'notes' => $material['notes'] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Additional work request submitted successfully',
                'data' => $additionalWork
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit additional work request: ' . $e->getMessage()
            ], 500);
        }
    }
}
