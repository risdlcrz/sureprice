<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users');
            $table->tinyInteger('rating')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('project_feedback');
    }
}; 