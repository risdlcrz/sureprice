<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Models\SupplierMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierController extends Controller
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

        $sortedSuppliers = Supplier::with(['evaluations' => function($query) {
                $query->latest('evaluation_date');
            }, 'metrics'])
            ->get()
            ->map(function($supplier) {
                $latestEvaluation = $supplier->getLatestEvaluation();
                return [
                    'id' => $supplier->id,
                    'company' => $supplier->company,
                    'materials' => $supplier->materials,
                    'price' => $supplier->price,
                    'contact_person' => $supplier->contact_person,
                    'designation' => $supplier->designation,
                    'email' => $supplier->email,
                    'mobile_number' => $supplier->mobile_number,
                    'telephone_number' => $supplier->telephone_number,
                    'address' => $supplier->address,
                    'business_reg_no' => $supplier->business_reg_no,
                    'supplier_type' => $supplier->supplier_type,
                    'business_size' => $supplier->business_size,
                    'years_operation' => $supplier->years_operation,
                    'payment_terms' => $supplier->payment_terms,
                    'vat_registered' => $supplier->vat_registered,
                    'use_sureprice' => $supplier->use_sureprice,
                    'bank_name' => $supplier->bank_name,
                    'account_name' => $supplier->account_name,
                    'account_number' => $supplier->account_number,
                    'engagement_score' => $latestEvaluation ? $latestEvaluation->engagement_score : 0,
                    'delivery_speed_score' => $latestEvaluation ? $latestEvaluation->delivery_speed_score : 0,
                    'performance_score' => $latestEvaluation ? $latestEvaluation->performance_score : 0,
                    'quality_score' => $latestEvaluation ? $latestEvaluation->quality_score : 0,
                    'cost_variance_score' => $latestEvaluation ? $latestEvaluation->cost_variance_score : 0,
                    'sustainability_score' => $latestEvaluation ? $latestEvaluation->sustainability_score : 0,
                    'final_score' => $supplier->getFinalScore()
                ];
            })
            ->sortBy(function($supplier) use ($category, $order) {
                return $supplier[$category];
            }, SORT_REGULAR, $order === 'desc');

        return view('admin.supplier-rankings', compact('sortedSuppliers', 'category', 'order', 'validCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:50',
            'supplier_type' => 'required|string|max:50',
            'products' => 'array',
            'dti_sec_registration' => 'nullable|file|max:10240',
            'mayors_permit' => 'nullable|file|max:10240',
            'valid_id' => 'nullable|file|max:10240',
            'company_profile' => 'nullable|file|max:10240',
            'price_list' => 'nullable|file|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $address = implode(', ', array_filter([
                $request->street,
                $request->city,
                $request->province,
                $request->zip_code
            ]));

            $materials = $request->has('products') ? implode(', ', $request->products) : '';

            $supplier = Supplier::create([
                'company' => $validated['company_name'],
                'materials' => $materials,
                'price' => 0,
                'contact_person' => $validated['contact_person'],
                'designation' => $request->designation,
                'email' => $validated['email'],
                'mobile_number' => $validated['mobile_number'],
                'telephone_number' => $request->telephone_number,
                'address' => $address,
                'business_reg_no' => $request->business_reg_no,
                'supplier_type' => $validated['supplier_type'],
                'business_size' => $request->business_size,
                'years_operation' => $request->years_operation,
                'payment_terms' => $request->payment_terms,
                'vat_registered' => $request->vat_registered === 'Yes',
                'use_sureprice' => $request->use_sureprice === 'Yes',
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ]);

            // Handle file uploads
            $uploadDir = 'uploads/supplier_docs/';
            if (!Storage::exists($uploadDir)) {
                Storage::makeDirectory($uploadDir);
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
                    $file->storeAs($uploadDir, $fileName);
                    
                    $supplier->update([
                        $field . '_path' => $uploadDir . $fileName
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.supplier-rankings')->with('success', 'Supplier added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.supplier-rankings')
                ->with('error', 'Failed to add supplier: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:50',
            'supplier_type' => 'required|string|max:50',
            'products' => 'array',
            'dti_sec_registration' => 'nullable|file|max:10240',
            'mayors_permit' => 'nullable|file|max:10240',
            'valid_id' => 'nullable|file|max:10240',
            'company_profile' => 'nullable|file|max:10240',
            'price_list' => 'nullable|file|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $address = implode(', ', array_filter([
                $request->street,
                $request->city,
                $request->province,
                $request->zip_code
            ]));

            $materials = $request->has('products') ? implode(', ', $request->products) : '';

            $supplier->update([
                'company' => $validated['company_name'],
                'materials' => $materials,
                'contact_person' => $validated['contact_person'],
                'designation' => $request->designation,
                'email' => $validated['email'],
                'mobile_number' => $validated['mobile_number'],
                'telephone_number' => $request->telephone_number,
                'address' => $address,
                'business_reg_no' => $request->business_reg_no,
                'supplier_type' => $validated['supplier_type'],
                'business_size' => $request->business_size,
                'years_operation' => $request->years_operation,
                'payment_terms' => $request->payment_terms,
                'vat_registered' => $request->vat_registered === 'Yes',
                'use_sureprice' => $request->use_sureprice === 'Yes',
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ]);

            // Handle file uploads
            $uploadDir = 'uploads/supplier_docs/';
            if (!Storage::exists($uploadDir)) {
                Storage::makeDirectory($uploadDir);
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
                    // Delete old file if exists
                    if ($supplier->{$field . '_path'}) {
                        Storage::delete($supplier->{$field . '_path'});
                    }

                    $file = $request->file($field);
                    $fileName = $supplier->id . '_' . $field . '_' . $file->getClientOriginalName();
                    $file->storeAs($uploadDir, $fileName);
                    
                    $supplier->update([
                        $field . '_path' => $uploadDir . $fileName
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.supplier-rankings')->with('success', 'Supplier updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.supplier-rankings')
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

            // Delete uploaded files
            $fileFields = [
                'dti_sec_registration',
                'accreditation_docs',
                'mayors_permit',
                'valid_id',
                'company_profile',
                'price_list'
            ];

            foreach ($fileFields as $field) {
                if ($supplier->{$field . '_path'}) {
                    Storage::delete($supplier->{$field . '_path'});
                }
            }

            $supplier->delete();

            DB::commit();
            return redirect()->route('admin.supplier-rankings')->with('success', 'Supplier deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.supplier-rankings')
                ->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }

    public function evaluate(Request $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'engagement_score' => 'required|numeric|min:1|max:5',
                'delivery_speed_score' => 'required|numeric|min:1|max:5',
                'performance_score' => 'required|numeric|min:1|max:5',
                'quality_score' => 'required|numeric|min:1|max:5',
                'cost_variance_score' => 'required|numeric|min:1|max:5',
                'sustainability_score' => 'required|numeric|min:1|max:5',
                'comments' => 'nullable|string'
            ]);

            $evaluation = SupplierEvaluation::create([
                'supplier_id' => $supplier->id,
                'evaluation_date' => now(),
                'engagement_score' => $validated['engagement_score'],
                'delivery_speed_score' => $validated['delivery_speed_score'],
                'performance_score' => $validated['performance_score'],
                'quality_score' => $validated['quality_score'],
                'cost_variance_score' => $validated['cost_variance_score'],
                'sustainability_score' => $validated['sustainability_score'],
                'comments' => $validated['comments']
            ]);

            DB::commit();
            return redirect()->route('admin.supplier-rankings')->with('success', 'Supplier evaluation submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.supplier-rankings')
                ->with('error', 'Failed to submit evaluation: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Supplier Data');

        // Define headers with sections
        $headers = [
            'Basic Information' => [
                'Company Name',
                'Type of Supplier',
                'Business Registration Number'
            ],
            'Contact Details' => [
                'Contact Person',
                'Designation',
                'Email',
                'Mobile Number',
                'Telephone Number',
                'Street Address',
                'City',
                'Province',
                'ZIP Code'
            ],
            'Business Details' => [
                'Years in Operation',
                'Products/Services',
                'Business Size'
            ],
            'Terms & Banking' => [
                'Payment Terms',
                'VAT Registered',
                'Use SurePrice',
                'Bank Name',
                'Account Name',
                'Account Number'
            ]
        ];

        // Style the headers
        $currentColumn = 1;
        foreach ($headers as $section => $fields) {
            // Add section header
            $sectionCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn);
            $sheet->setCellValue($sectionCol . '1', $section);
            $sheet->getStyle($sectionCol . '1')->getFont()->setBold(true);
            $sheet->getStyle($sectionCol . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4F81BD');
            $sheet->getStyle($sectionCol . '1')->getFont()->getColor()->setRGB('FFFFFF');

            // Merge cells for section header if multiple fields
            if (count($fields) > 1) {
                $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn + count($fields) - 1);
                $sheet->mergeCells($sectionCol . '1:' . $endCol . '1');
            }

            // Add field headers
            foreach ($fields as $idx => $field) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn + $idx);
                $sheet->setCellValue($col . '2', $field);
                $sheet->getStyle($col . '2')->getFont()->setBold(true);
                $sheet->getStyle($col . '2')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('DCE6F1');
            }
            $currentColumn += count($fields);
        }

        // Add example data
        $exampleData = [
            'ABC Construction Supply',
            'Material Supplier',
            'REG123456789',
            'John Doe',
            'Sales Manager',
            'john.doe@example.com',
            '09123456789',
            '(02) 8123-4567',
            '123 Main Street',
            'Makati City',
            'Metro Manila',
            '1234',
            '5',
            'Steel Bars, Cement, Gravel',
            'Medium Enterprise',
            '30 days',
            'Yes',
            'Yes',
            'BDO',
            'ABC Construction Supply Inc',
            '1234567890'
        ];

        $col = 1;
        foreach ($exampleData as $value) {
            $sheet->setCellValueByColumnAndRow($col, 3, $value);
            $col++;
        }

        // Add data validation
        $validations = [
            'Type of Supplier' => ['Individual', 'Company', 'Contractor', 'Material Supplier', 'Equipment Rental'],
            'Business Size' => ['Solo', 'Small Enterprise', 'Medium', 'Large'],
            'Payment Terms' => ['7 days', '15 days', '30 days'],
            'VAT Registered' => ['Yes', 'No'],
            'Use SurePrice' => ['Yes', 'No']
        ];

        // Find columns for validation
        $headerRow = $sheet->getRowIterator(2)->current();
        $cellIterator = $headerRow->getCellIterator();
        $headerMap = [];
        foreach ($cellIterator as $cell) {
            $headerMap[$cell->getValue()] = $cell->getColumn();
        }

        // Apply validations
        foreach ($validations as $field => $options) {
            if (isset($headerMap[$field])) {
                $col = $headerMap[$field];
                $validation = $sheet->getCell($col . '3')->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"' . implode(',', $options) . '"');

                // Copy validation to next 100 rows
                for ($i = 4; $i <= 100; $i++) {
                    $sheet->getCell($col . $i)->setDataValidation(clone $validation);
                }
            }
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instructions');
        
        $instructions = [
            'Instructions for Filling Out the Template',
            '',
            '1. Basic Information:',
            '   • Company Name: Full registered business name',
            '   • Type of Supplier: Select from dropdown list',
            '   • Business Registration: DTI/SEC registration number',
            '',
            '2. Contact Details:',
            '   • All fields should be current and active',
            '   • Mobile: Use format 09XXXXXXXXX',
            '   • Email: Use valid email format',
            '',
            '3. Business Details:',
            '   • Years in Operation: Numeric only',
            '   • Products/Services: List main offerings',
            '   • Business Size: Select from dropdown',
            '',
            '4. Terms & Banking:',
            '   • Payment Terms: Select from options',
            '   • Bank Details: Provide complete information',
            '',
            'Important Notes:',
            '- Required fields are marked with headers in blue',
            '- Use dropdown menus where available',
            '- Follow the example row format',
            '- Save as Excel (.xlsx) format'
        ];

        foreach ($instructions as $idx => $text) {
            $instructionSheet->setCellValue('A' . ($idx + 1), $text);
            if (strpos($text, 'Instructions') === 0 || strpos($text, 'Important Notes') === 0) {
                $instructionSheet->getStyle('A' . ($idx + 1))->getFont()->setBold(true)->setSize(14);
            }
        }

        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set active sheet to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="supplier_template.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('import_file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            $headers = array_shift($rows);
            
            $imported = 0;
            $skipped = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($rows as $index => $row) {
                try {
                    if (empty($row[0])) continue; // Skip empty rows
                    
                    // Map the columns to database fields
                    $data = [
                        'company' => trim($row[0]),
                        'supplier_type' => trim($row[1]),
                        'business_reg_no' => trim($row[2]),
                        'contact_person' => trim($row[3]),
                        'designation' => trim($row[4]),
                        'email' => trim($row[5]),
                        'mobile_number' => trim($row[6]),
                        'telephone_number' => trim($row[7]),
                        'address' => implode(', ', array_filter([trim($row[8]), trim($row[9]), trim($row[10]), trim($row[11])])),
                        'years_operation' => intval($row[12]),
                        'materials' => trim($row[13]),
                        'business_size' => trim($row[14]),
                        'payment_terms' => trim($row[15]),
                        'vat_registered' => trim($row[16]) === 'Yes',
                        'use_sureprice' => trim($row[17]) === 'Yes',
                        'bank_name' => trim($row[18]),
                        'account_name' => trim($row[19]),
                        'account_number' => trim($row[20])
                    ];
                    
                    // Validate required fields
                    $required_fields = ['company', 'email', 'contact_person'];
                    foreach ($required_fields as $field) {
                        if (empty($data[$field])) {
                            throw new \Exception("Missing required field: $field");
                        }
                    }
                    
                    // Create supplier
                    Supplier::create($data);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            if ($imported > 0) {
                DB::commit();
                return redirect()->route('admin.supplier-rankings')
                    ->with('success', "Imported $imported suppliers successfully." . 
                           ($skipped > 0 ? " Skipped $skipped invalid entries." : ""))
                    ->with('import_errors', $errors);
            } else {
                throw new \Exception("No valid suppliers found to import");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.supplier-rankings')
                ->with('error', "Error importing file: " . $e->getMessage())
                ->with('import_errors', $errors ?? []);
        }
    }
} 