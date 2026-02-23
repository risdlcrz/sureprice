<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\Inventory::pluck('id','material_id');
foreach ($items as $material_id => $inventory_id) {
    echo "material $material_id -> inventory $inventory_id\n";
}
