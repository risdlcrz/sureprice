<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The "Request Additional Work" form (project timeline / warranty pages) saves these fields, but the
 * table never had them, and `amount` was required without a default — so every submission failed.
 * Additive only: new nullable columns, and amount defaults to 0 until the work is priced.
 */
return new class extends Migration
{
    private array $columns = ['work_type', 'estimated_hours', 'required_skills', 'labor_notes',
        'preferred_start_date', 'preferred_end_date', 'timeline_notes', 'additional_notes'];

    public function up(): void
    {
        Schema::table('additional_works', function (Blueprint $table) {
            if (!Schema::hasColumn('additional_works', 'work_type')) {
                $table->string('work_type')->nullable()->after('contract_id');
                $table->decimal('estimated_hours', 8, 2)->nullable();
                $table->text('required_skills')->nullable();
                $table->text('labor_notes')->nullable();
                $table->date('preferred_start_date')->nullable();
                $table->date('preferred_end_date')->nullable();
                $table->text('timeline_notes')->nullable();
                $table->text('additional_notes')->nullable();
            }
            $table->decimal('amount', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('additional_works', function (Blueprint $table) {
            $table->dropColumn(array_filter($this->columns, fn ($c) => Schema::hasColumn('additional_works', $c)));
        });
    }
};
