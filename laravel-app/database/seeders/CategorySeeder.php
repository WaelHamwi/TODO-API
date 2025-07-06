<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Work', 'Personal', 'Shopping', 'Others'];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
