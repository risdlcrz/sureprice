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

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'ID', 'Contract Number', 'Client Name', 'Product Name', 'Serial Number',
            'Issue Description', 'Status', 'Submitted Date', 'Reviewed Date', 'Admin Notes'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($warrantyRequests as $request) {
            $sheet->setCellValue('A' . $row, $request->id);
            $sheet->setCellValue('B' . $row, $request->contract->contract_number);
            $sheet->setCellValue('C' . $row, $request->contract->client->name);
            $sheet->setCellValue('D' . $row, $request->product_name);
            $sheet->setCellValue('E' . $row, $request->serial_number);
            $sheet->setCellValue('F' . $row, $request->issue_description);
            $sheet->setCellValue('G' . $row, ucfirst($request->status));
            $sheet->setCellValue('H' . $row, $request->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('I' . $row, $request->reviewed_at ? $request->reviewed_at->format('Y-m-d H:i:s') : 'N/A');
            $sheet->setCellValue('J' . $row, $request->admin_notes ?? 'N/A');
            $row++;
        }

        // Style data
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $sheet->getStyle('A2:J' . ($row - 1))->applyFromArray($dataStyle);

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'warranty_requests_' . date('Y-m-d_His') . '.xlsx';
        $path = storage_path('app/public/' . $filename);
        $writer->save($path);

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
