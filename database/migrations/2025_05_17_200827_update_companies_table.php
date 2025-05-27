<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            // 1. Add core relationship field
            if (!Schema::hasColumn('companies', 'user_id')) {
                $table->foreignId('user_id')
                      ->constrained()
                      ->cascadeOnDelete()
                      ->after('id');
            }

            // 2. Add account info fields
            $this->addColumnIfMissing($table, 'username', 'string', [
                'unique' => true,
                'after' => 'user_id'
            ]);

            $this->addColumnIfMissing($table, 'email', 'string', [
                'length' => 100,
                'after' => 'username'
            ]);

            // 3. Add basic company info fields
            $this->addColumnIfMissing($table, 'company_name', 'string', [
                'length' => 100,
                'after' => 'email'
            ]);

            $this->addColumnIfMissing($table, 'supplier_type', 'enum', [
                'values' => ['Individual', 'Contractor', 'Material Supplier', 'Equipment Rental', 'Other'],
                'after' => 'company_name'
            ]);

            $this->addColumnIfMissing($table, 'other_supplier_type', 'string', [
                'length' => 100,
                'nullable' => true,
                'after' => 'supplier_type'
            ]);

            $this->addColumnIfMissing($table, 'business_reg_no', 'string', [
                'length' => 50,
                'nullable' => true,
                'after' => 'other_supplier_type'
            ]);

            // 4. Add contact details
            $this->addColumnIfMissing($table, 'contact_person', 'string', [
                'length' => 100,
                'after' => 'business_reg_no'
            ]);

            $this->addColumnIfMissing($table, 'designation', 'string', [
                'length' => 100,
                'nullable' => true,
                'after' => 'contact_person'
            ]);

            $this->addColumnIfMissing($table, 'mobile_number', 'string', [
                'length' => 20,
                'after' => 'designation'
            ]);

            $this->addColumnIfMissing($table, 'telephone_number', 'string', [
                'length' => 20,
                'nullable' => true,
                'after' => 'mobile_number'
            ]);

            // 5. Add address fields
            $addressFields = [
                'street' => ['length' => 255, 'nullable' => true],
                'city' => ['length' => 100],
                'province' => ['length' => 100],
                'zip_code' => ['length' => 10]
            ];

            foreach ($addressFields as $field => $options) {
                $this->addColumnIfMissing($table, $field, 'string', $options);
            }

            // 6. Add business details
            $this->addColumnIfMissing($table, 'years_operation', 'integer', [
                'nullable' => true,
                'after' => 'zip_code'
            ]);

            $this->addColumnIfMissing($table, 'business_size', 'enum', [
                'values' => ['Solo', 'Small Enterprise', 'Medium', 'Large'],
                'nullable' => true,
                'after' => 'years_operation'
            ]);

            $this->addColumnIfMissing($table, 'service_areas', 'text', [
                'nullable' => true,
                'after' => 'business_size'
            ]);

            // 7. Add pricing/terms fields
            $this->addColumnIfMissing($table, 'vat_registered', 'boolean', [
                'default' => false,
                'after' => 'service_areas'
            ]);

            $this->addColumnIfMissing($table, 'use_sureprice', 'boolean', [
                'default' => false,
                'after' => 'vat_registered'
            ]);

            $this->addColumnIfMissing($table, 'payment_terms', 'enum', [
                'values' => ['7 days', '15 days', '30 days'],
                'nullable' => true,
                'after' => 'use_sureprice'
            ]);

            // 8. Add system fields
            $this->addColumnIfMissing($table, 'status', 'enum', [
                'values' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'payment_terms'
            ]);
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            // List all columns that were added in the up() method
            $columnsToDrop = [
                'user_id',
                'username',
                'email',
                'company_name',
                'supplier_type',
                'other_supplier_type',
                'business_reg_no',
                'contact_person',
                'designation',
                'mobile_number',
                'telephone_number',
                'street',
                'city',
                'province',
                'zip_code',
                'years_operation',
                'business_size',
                'service_areas',
                'vat_registered',
                'use_sureprice',
                'payment_terms',
                'status'
            ];

            // Drop foreign key constraints first
            $table->dropForeign(['user_id']);

            // Drop columns only if they exist
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Helper method to safely add columns
     */
    protected function addColumnIfMissing(Blueprint $table, string $name, string $type, array $options = [])
    {
        if (Schema::hasColumn('companies', $name)) {
            return;
        }

        $column = null;

        // Handle different column types
        switch ($type) {
            case 'enum':
                $column = $table->enum($name, $options['values']);
                break;
            case 'string':
                $column = $table->string($name, $options['length'] ?? null);
                break;
            default:
                $column = $table->{$type}($name);
        }

        // Apply modifiers
        if ($options['nullable'] ?? false) {
            $column->nullable();
        }

        if (isset($options['default'])) {
            $column->default($options['default']);
        }

        if (isset($options['unique']) && $options['unique']) {
            $column->unique();
        }

        if (isset($options['after'])) {
            $column->after($options['after']);
        }
    }
};