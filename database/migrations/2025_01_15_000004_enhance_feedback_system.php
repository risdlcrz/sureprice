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
        // Enhance project feedback table
        Schema::table('project_feedback', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('detailed_feedback')->nullable();
            $table->json('rating_breakdown')->nullable(); // Store detailed ratings (quality, timeliness, communication, etc.)
            $table->boolean('recommend_to_others')->nullable();
            $table->string('feedback_type')->default('client'); // client, internal
            $table->timestamps();
        });

        // Enhance delivery feedback table
        Schema::table('delivery_feedback', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('detailed_feedback')->nullable();
            $table->json('rating_breakdown')->nullable(); // Store detailed ratings (delivery_time, packaging, condition, etc.)
            $table->boolean('on_time_delivery')->nullable();
            $table->boolean('proper_packaging')->nullable();
            $table->boolean('items_as_ordered')->nullable();
            $table->string('feedback_type')->default('warehouse'); // warehouse, supplier
            $table->timestamps();
        });

        // Create supplier evaluation table for ranking
        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('evaluated_by');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->decimal('overall_rating', 3, 2);
            $table->json('rating_breakdown');
            $table->text('comments')->nullable();
            $table->string('evaluation_type'); // delivery, quality, communication, etc.
            $table->timestamps();
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('evaluated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'detailed_feedback',
                'rating_breakdown',
                'recommend_to_others',
                'feedback_type',
                'created_at',
                'updated_at'
            ]);
        });

        Schema::table('delivery_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'detailed_feedback',
                'rating_breakdown',
                'on_time_delivery',
                'proper_packaging',
                'items_as_ordered',
                'feedback_type',
                'created_at',
                'updated_at'
            ]);
        });

        Schema::dropIfExists('supplier_evaluations');
    }
}; 