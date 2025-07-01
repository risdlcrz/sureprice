<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // No longer needed: foreign keys are defined in table creation migrations.
    }

    public function down()
    {
        // No longer needed: foreign keys are dropped with the tables.
    }
}; 