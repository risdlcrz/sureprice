<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'minimum_stock') && !Schema::hasColumn('stocks', 'threshold')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->renameColumn('minimum_stock', 'threshold');
            });
        }
    }
    public function down() {
        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'threshold') && !Schema::hasColumn('stocks', 'minimum_stock')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->renameColumn('threshold', 'minimum_stock');
            });
        }
    }
}; 