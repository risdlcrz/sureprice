<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            // Add project_id column
            $table->foreignId('project_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Populate project_id based on existing contract_id relationships
        // Portable (MySQL + SQLite) instead of UPDATE ... JOIN
        DB::statement('UPDATE project_tasks SET project_id = (SELECT p.id FROM projects p WHERE p.contract_id = project_tasks.contract_id LIMIT 1) WHERE project_id IS NULL');

        // Make project_id not nullable after populating data
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
