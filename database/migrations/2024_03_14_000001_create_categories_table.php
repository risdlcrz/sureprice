<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default categories
        DB::table('categories')->insert([
            ['name' => 'Construction', 'slug' => 'construction', 'description' => 'Construction materials'],
            ['name' => 'Electrical', 'slug' => 'electrical', 'description' => 'Electrical materials'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'description' => 'Plumbing materials'],
            ['name' => 'Finishing', 'slug' => 'finishing', 'description' => 'Finishing materials'],
            ['name' => 'Tools', 'slug' => 'tools', 'description' => 'Tools and equipment'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Other materials']
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}; 