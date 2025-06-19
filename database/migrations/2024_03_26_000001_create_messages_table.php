<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if conversations table exists
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
                $table->string('status')->default('active');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        // Check if messages table exists
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->text('content')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
}; 