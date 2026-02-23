<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$kernel=$app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\InventoryController();

foreach ([1,29] as $id) {
    echo "testing edit($id):\n";
    // simulate call
    
    try {
        $resp = $controller->edit($id);
        if (method_exists($resp, 'getStatusCode')) {
            echo "response status " . $resp->getStatusCode() . "\n";
        } else {
            echo "returned ".get_class($resp)."\n";
        }
    } catch (\Exception $e) {
        echo "exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
    }
}
