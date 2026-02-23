<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\InventoryController();
$response = $controller->index();
// the response is a View, we can inspect materials
$view = $response->getData();
$materials = $view['materials'];
foreach ($materials as $m) {
    echo "material {$m->id} -> inventory ".($m->primary_inventory? $m->primary_inventory->id:'none')."\n";
}
