<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$material = \App\Models\Material::find(1);
if ($material) {
    if (!\App\Models\Inventory::where('material_id', 1)->exists()) {
        \App\Models\Inventory::create([
            'material_id' => 1,
            'quantity' => 0,
            'unit' => $material->unit,
            'location' => null,
            'status' => 'active',
            'minimum_threshold' => 0,
        ]);
        echo "created inventory record for material 1\n";
    } else {
        echo "inventory already exists for material 1\n";
    }
} else {
    echo "material 1 not found\n";
}
