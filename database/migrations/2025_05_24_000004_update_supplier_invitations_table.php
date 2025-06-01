<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the categories pivot table first
        Schema::dropIfExists('category_supplier_invitation');

        // Update the supplier_invitations table
        Schema::table('supplier_invitations', function (Blueprint $table) {
            // Drop existing columns that we don't need
            $table->dropColumn([
                'business_type',
                'position',
                'address',
                'tax_number',
                'registration_number',
                'notes'
            ]);

            // Rename contact_person to contact_name
            $table->renameColumn('contact_person', 'contact_name');

            // Add new columns
            if (!Schema::hasColumn('supplier_invitations', 'project_id')) {
                $table->foreignId('project_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('supplier_invitations', 'invitation_code')) {
                $table->string('invitation_code')->after('project_id')->unique();
            }
            if (!Schema::hasColumn('supplier_invitations', 'message')) {
                $table->text('message')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('supplier_invitations', 'due_date')) {
                $table->date('due_date')->after('message');
            }
        });

        // Create materials pivot table
        if (!Schema::hasTable('supplier_invitation_materials')) {
            Schema::create('supplier_invitation_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_invitation_id')->constrained()->onDelete('cascade');
                $table->foreignId('material_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            // Restore original columns
            $table->enum('business_type', ['corporation', 'partnership', 'sole_proprietorship', 'other'])->after('company_name');
            $table->string('position')->after('contact_name');
            $table->text('address')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('address');
            $table->string('registration_number')->nullable()->after('tax_number');
            $table->text('notes')->nullable()->after('status');

            // Rename contact_name back to contact_person
            $table->renameColumn('contact_name', 'contact_person');

            // Drop new columns
            $table->dropForeign(['project_id']);
            $table->dropColumn([
                'project_id',
                'invitation_code',
                'message',
                'due_date'
            ]);
        });

        // Drop materials pivot table
        Schema::dropIfExists('supplier_invitation_materials');

        // Restore categories pivot table
        Schema::create('category_supplier_invitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invitation_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
}; 