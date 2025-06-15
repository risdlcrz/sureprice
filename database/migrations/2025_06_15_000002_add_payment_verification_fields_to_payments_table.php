<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('client_payment_proof')->nullable()->after('reference_number');
            $table->string('client_payment_method')->nullable()->after('client_payment_proof');
            $table->string('client_reference_number')->nullable()->after('client_payment_method');
            $table->decimal('client_paid_amount', 15, 2)->nullable()->after('client_reference_number');
            $table->date('client_paid_date')->nullable()->after('client_paid_amount');
            $table->text('client_notes')->nullable()->after('client_paid_date');

            $table->string('admin_payment_proof')->nullable()->after('client_notes');
            $table->string('admin_payment_method')->nullable()->after('admin_payment_proof');
            $table->string('admin_reference_number')->nullable()->after('admin_payment_method');
            $table->decimal('admin_received_amount', 15, 2)->nullable()->after('admin_reference_number');
            $table->date('admin_received_date')->nullable()->after('admin_received_amount');
            $table->text('admin_notes')->nullable()->after('admin_received_date');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'client_payment_proof',
                'client_payment_method',
                'client_reference_number',
                'client_paid_amount',
                'client_paid_date',
                'client_notes',
                'admin_payment_proof',
                'admin_payment_method',
                'admin_reference_number',
                'admin_received_amount',
                'admin_received_date',
                'admin_notes',
            ]);
        });
    }
}; 