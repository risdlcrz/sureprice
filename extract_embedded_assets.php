<?php

/**
 * Script to identify Blade files with embedded CSS and JS
 * This helps with the systematic extraction of embedded assets
 */

$viewsPath = 'resources/views';
$files = [];

function scanDirectory($path) {
    global $files;
    
    $items = scandir($path);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $fullPath = $path . '/' . $item;
        
        if (is_dir($fullPath)) {
            scanDirectory($fullPath);
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'blade.php') {
            $content = file_get_contents($fullPath);
            
            // Check for embedded CSS and JS
            $hasStyle = preg_match('/<style>/', $content);
            $hasScript = preg_match('/<script>/', $content);
            
            if ($hasStyle || $hasScript) {
                $files[] = [
                    'path' => $fullPath,
                    'has_style' => $hasStyle,
                    'has_script' => $hasScript,
                    'size' => filesize($fullPath)
                ];
            }
        }
    }
}

scanDirectory($viewsPath);

echo "Files with embedded CSS and/or JS:\n";
echo "==================================\n\n";

foreach ($files as $file) {
    echo "File: {$file['path']}\n";
    echo "Has Style: " . ($file['has_style'] ? 'Yes' : 'No') . "\n";
    echo "Has Script: " . ($file['has_script'] ? 'Yes' : 'No') . "\n";
    echo "Size: " . number_format($file['size']) . " bytes\n";
    echo "---\n";
}

echo "\nTotal files found: " . count($files) . "\n";

// Group by directory
$byDirectory = [];
foreach ($files as $file) {
    $dir = dirname($file['path']);
    if (!isset($byDirectory[$dir])) {
        $byDirectory[$dir] = [];
    }
    $byDirectory[$dir][] = $file;
}

echo "\nFiles by directory:\n";
echo "==================\n\n";

foreach ($byDirectory as $dir => $dirFiles) {
    echo "Directory: $dir\n";
    foreach ($dirFiles as $file) {
        echo "  - " . basename($file['path']) . "\n";
    }
    echo "\n";
} 