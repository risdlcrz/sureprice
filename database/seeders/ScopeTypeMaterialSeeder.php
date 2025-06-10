<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScopeType;
use App\Models\Material;

class ScopeTypeMaterialSeeder extends Seeder
{
    public function run()
    {
        $materials = Material::all();
        $scopeTypes = ScopeType::all();

        if ($materials->count() === 0 || $scopeTypes->count() === 0) {
            $this->command->warn('No materials or scope types found. Seeder skipped.');
            return;
        }

        foreach ($scopeTypes as $scopeType) {
            // Randomly pick 2-3 materials for each scope type
            $materialIds = $materials->random(min(3, $materials->count()))->pluck('id')->toArray();
            $scopeType->materials()->sync($materialIds);
        }

        $this->command->info('ScopeTypeMaterialSeeder: Materials assigned to each scope type.');
    }
} 