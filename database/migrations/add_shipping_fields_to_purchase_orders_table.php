<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('shipped_at')->nullable()->after('status');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->text('shipping_note')->nullable()->after('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['shipped_at', 'delivered_at', 'shipping_note']);
        });
    }
}; 