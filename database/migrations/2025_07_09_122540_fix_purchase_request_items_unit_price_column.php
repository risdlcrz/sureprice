<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            // Check if unit_price exists and estimated_unit_price doesn't, then rename
            if (Schema::hasColumn('purchase_request_items', 'unit_price') && !Schema::hasColumn('purchase_request_items', 'estimated_unit_price')) {
                $table->renameColumn('unit_price', 'estimated_unit_price');
            }
            
            // Add estimated_unit_price if it doesn't exist (and unit_price also doesn't exist)
            if (!Schema::hasColumn('purchase_request_items', 'estimated_unit_price') && !Schema::hasColumn('purchase_request_items', 'unit_price')) {
                $table->decimal('estimated_unit_price', 12, 2)->default(0)->after('unit');
            }
            
            // Add other missing columns
            if (!Schema::hasColumn('purchase_request_items', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('material_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('purchase_request_items', 'description')) {
                $table->text('description')->nullable()->after('supplier_id');
            }
            
            if (!Schema::hasColumn('purchase_request_items', 'notes')) {
                $table->text('notes')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('purchase_request_items', 'preferred_brand')) {
                $table->string('preferred_brand')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('purchase_request_items', 'preferred_supplier_id')) {
                $table->unsignedBigInteger('preferred_supplier_id')->nullable()->after('preferred_brand');
                $table->foreign('preferred_supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('purchase_request_items', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
            if (Schema::hasColumn('purchase_request_items', 'preferred_supplier_id')) {
                $table->dropForeign(['preferred_supplier_id']);
                $table->dropColumn('preferred_supplier_id');
            }
            
            // Drop other columns
            if (Schema::hasColumn('purchase_request_items', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('purchase_request_items', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('purchase_request_items', 'preferred_brand')) {
                $table->dropColumn('preferred_brand');
            }
            
            // Rename back if needed
            if (Schema::hasColumn('purchase_request_items', 'estimated_unit_price') && !Schema::hasColumn('purchase_request_items', 'unit_price')) {
                $table->renameColumn('estimated_unit_price', 'unit_price');
            }
        });
    }
};
