<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Removed creation of 'purchase_order_items' table to avoid duplicate table creation and foreign key errors.
    }

    public function down(): void
    {
        // Removed dropping of 'purchase_order_items' table to avoid duplicate table dropping.
    }
}; 