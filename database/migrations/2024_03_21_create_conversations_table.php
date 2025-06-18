<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('private'); // private, group
            $table->string('name')->nullable(); // for group chats
            $table->timestamps();
            
            // Ensure unique conversation between two users
            $table->unique(['user_one_id', 'user_two_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}; 