<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // MySQL only: alter enum to add 'contractor'
        DB::statement("ALTER TABLE employees MODIFY role ENUM('procurement','warehousing','contractor') NOT NULL");
    }

    public function down()
    {
        // Revert to original
        DB::statement("ALTER TABLE employees MODIFY role ENUM('procurement','warehousing') NOT NULL");
    }
}; 