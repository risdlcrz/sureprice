<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$materials = \App\Models\Material::with(['inventory'])->get();
foreach ($materials as $m) {
    echo "Material {$m->id} inventory ids: ";
    foreach ($m->inventory as $inv) { echo $inv->id.' '; }
    if($m->inventory->isEmpty()) echo 'none';
    echo "\n";
}
