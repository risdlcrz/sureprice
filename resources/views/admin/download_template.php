<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = [
    'Material Name',
    'Total Stock',
    'Threshold',
    'Unit of Measurement',
    'Category',
    'Purchase Price'
];

// Add headers
foreach ($headers as $index => $header) {
    $column = chr(65 + $index); // Convert number to letter (A, B, C, etc.)
    $sheet->setCellValue($column . '1', $header);
    $sheet->getStyle($column . '1')->getFont()->setBold(true);
}

// Add example row
$example = [
    'Example Material',
    '100',
    '20',
    'pcs',
    'Tools',
    '99.99'
];

foreach ($example as $index => $value) {
    $column = chr(65 + $index);
    $sheet->setCellValue($column . '2', $value);
}

// Add dropdown lists for Unit and Category
$unitList = ['pcs', 'kg', 'm', 'm²', 'm³', 'l', 'bags', 'rolls'];
$categoryList = ['Cement', 'Steel', 'Wood', 'Plumbing', 'Electrical', 'Paint', 'Tools', 'Other'];

// Set data validation for Unit column (column D)
$validation = $sheet->getCell('D2')->getDataValidation();
$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$validation->setFormula1('"' . implode(',', $unitList) . '"');
$validation->setAllowBlank(false);
$validation->setShowDropDown(true);
$validation->setPromptTitle('Unit of Measurement');
$validation->setPrompt('Choose a unit from the list');

// Set data validation for Category column (column E)
$validation = $sheet->getCell('E2')->getDataValidation();
$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$validation->setFormula1('"' . implode(',', $categoryList) . '"');
$validation->setAllowBlank(false);
$validation->setShowDropDown(true);
$validation->setPromptTitle('Category');
$validation->setPrompt('Choose a category from the list');

// Auto-size columns
foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="materials_template.xlsx"');
header('Cache-Control: max-age=0');

// Create Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output'); 