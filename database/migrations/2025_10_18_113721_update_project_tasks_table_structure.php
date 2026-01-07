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
        Schema::table('project_tasks', function (Blueprint $table) {
            // Rename title to name
            $table->renameColumn('title', 'name');
            
            // Rename end_date to due_date
            $table->renameColumn('end_date', 'due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            // Revert the changes
            $table->renameColumn('name', 'title');
            $table->renameColumn('due_date', 'end_date');
        });
    }
};
