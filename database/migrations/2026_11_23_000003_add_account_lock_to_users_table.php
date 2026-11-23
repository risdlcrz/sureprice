<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin account lock: an admin (or too many failed sign-ins) locks a user account; locked users cannot
 * sign in or act. See User::lockBy() / unlock() and EnsureAccountIsNotLocked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('last_login_at');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            // null when the lock came from too many failed sign-ins
            $table->foreignId('locked_by')->nullable()->after('locked_at')->constrained('users')->nullOnDelete();
            $table->string('lock_reason')->nullable()->after('locked_by');
            $table->unsignedSmallInteger('failed_login_attempts')->default(0)->after('lock_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('locked_by');
            $table->dropColumn(['is_locked', 'locked_at', 'lock_reason', 'failed_login_attempts']);
        });
    }
};
