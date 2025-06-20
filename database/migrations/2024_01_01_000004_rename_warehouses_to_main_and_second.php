<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() {
        DB::table('warehouses')->where('name', 'Warehouse A')->update(['name' => 'Main Warehouse']);
        DB::table('warehouses')->where('name', 'Warehouse B')->update(['name' => '2nd Warehouse']);
    }
    public function down() {
        DB::table('warehouses')->where('name', 'Main Warehouse')->update(['name' => 'Warehouse A']);
        DB::table('warehouses')->where('name', '2nd Warehouse')->update(['name' => 'Warehouse B']);
    }
}; 