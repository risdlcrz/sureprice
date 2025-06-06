<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Storage;

class SupplierRankingService
{
    protected $weights = [
        'engagement' => 0.15,
        'delivery' => 0.25,
        'performance' => 0.20,
        'quality' => 0.20,
        'cost' => 0.10,
        'sustainability' => 0.10
    ];

    public function calculateRankings(Collection $suppliers)
    {
        return $suppliers->map(function ($supplier) {
            $latestEvaluation = $supplier->evaluations()->latest()->first();
            $metrics = $supplier->metrics;
            
            if (!$latestEvaluation || !$metrics) {
                return [
                    'supplier' => $supplier,
                    'score' => 0,
                    'rank' => null
                ];
            }

            return [
                'supplier' => $supplier,
                'score' => $this->calculateFinalScore($latestEvaluation, $metrics),
                'rank' => null
            ];
        })->sortByDesc('score')->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }

    protected function calculateFinalScore($evaluation, $metrics)
    {
        // Calculate objective metrics
        $deliveryObj = ($metrics->ontime_deliveries / max(1, $metrics->total_deliveries)) * 100;
        $qualityObj = (1 - ($metrics->defective_units / max(1, $metrics->total_units))) * 100;
        $costObj = (($metrics->actual_cost - $metrics->estimated_cost) / max(1, $metrics->estimated_cost)) * 100;
        
        // Combine subjective and objective scores
        $deliveryScore = ($evaluation->delivery_speed_score * 0.5 + $deliveryObj * 0.5);
        $qualityScore = ($evaluation->quality_score * 0.5 + $qualityObj * 0.5);
        $costScore = ($evaluation->cost_variance_score * 0.5 + (100 - abs($costObj)) * 0.5);
        
        return (
            $this->weights['engagement'] * $evaluation->engagement_score +
            $this->weights['delivery'] * $deliveryScore +
            $this->weights['performance'] * $evaluation->performance_score +
            $this->weights['quality'] * $qualityScore +
            $this->weights['cost'] * $costScore +
            $this->weights['sustainability'] * $evaluation->sustainability_score
        ) / 5;
    }

    public function generateTemplate()
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

        $currentColumn = 1;
        foreach ($headers as $section => $fields) {
            $sectionCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn);
            $sheet->setCellValue($sectionCol . '1', $section);
            $this->styleHeader($sheet, $sectionCol . '1', true);

            if (count($fields) > 1) {
                $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn + count($fields) - 1);
                $sheet->mergeCells($sectionCol . '1:' . $endCol . '1');
            }

            foreach ($fields as $idx => $field) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn + $idx);
                $sheet->setCellValue($col . '2', $field);
                $this->styleHeader($sheet, $col . '2', false);
            }
            $currentColumn += count($fields);
        }

        // Add data validation
        $this->addDataValidation($sheet);

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $this->addInstructionsSheet($spreadsheet);

        return $this->downloadSpreadsheet($spreadsheet, 'supplier_template.xlsx');
    }

    public function generateMaterialsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Materials List');

        // Add headers
        $headers = ['Material Code', 'Name', 'Category', 'Unit', 'Price', 'Lead Time (days)'];
        foreach ($headers as $idx => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue($col . '1', $header);
            $this->styleHeader($sheet, $col . '1', true);
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->downloadSpreadsheet($spreadsheet, 'materials_template.xlsx');
    }

    protected function styleHeader($sheet, $cell, $isPrimary)
    {
        $style = $sheet->getStyle($cell);
        $style->getFont()->setBold(true);
        
        if ($isPrimary) {
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4F81BD');
            $style->getFont()->getColor()->setRGB('FFFFFF');
        } else {
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('DCE6F1');
        }
    }

    protected function addDataValidation($sheet)
    {
        $validations = [
            'Type of Supplier' => ['Individual', 'Company', 'Contractor', 'Material Supplier', 'Equipment Rental'],
            'Business Size' => ['Solo', 'Small Enterprise', 'Medium', 'Large'],
            'Payment Terms' => ['7 days', '15 days', '30 days'],
            'VAT Registered' => ['Yes', 'No'],
            'Use SurePrice' => ['Yes', 'No']
        ];

        // Find columns for validation
        $headerRow = 2;
        $headerRange = $sheet->getRowIterator($headerRow)->current();
        foreach ($headerRange->getCellIterator() as $cell) {
            $header = $cell->getValue();
            if (isset($validations[$header])) {
                $validation = $cell->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"' . implode(',', $validations[$header]) . '"');

                // Copy validation to next 100 rows
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell($cell->getColumn() . $i)
                        ->setDataValidation(clone $validation);
                }
            }
        }
    }

    protected function addInstructionsSheet($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Instructions');
        
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
            $sheet->setCellValue('A' . ($idx + 1), $text);
            if (strpos($text, 'Instructions') === 0 || strpos($text, 'Important Notes') === 0) {
                $sheet->getStyle('A' . ($idx + 1))->getFont()->setBold(true)->setSize(14);
            }
        }

        $sheet->getColumnDimension('A')->setWidth(60);
    }

    protected function downloadSpreadsheet($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/temp/' . $filename);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer->save($path);
        
        return response()->download($path)->deleteFileAfterSend();
    }
} 