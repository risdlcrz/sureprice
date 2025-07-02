<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'General', 'slug' => 'general', 'description' => 'General construction materials'],
            ['name' => 'Construction', 'slug' => 'construction', 'description' => 'Construction materials'],
            ['name' => 'Electrical', 'slug' => 'electrical', 'description' => 'Electrical materials'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'description' => 'Plumbing materials'],
            ['name' => 'Finishing', 'slug' => 'finishing', 'description' => 'Finishing materials'],
            ['name' => 'Tools', 'slug' => 'tools', 'description' => 'Tools and equipment'],
            ['name' => 'Paint', 'slug' => 'paint', 'description' => 'Paint and coatings'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
} 