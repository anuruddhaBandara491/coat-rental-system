<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class AddCategoryNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemCategory::insert([
            [
                'id' => 4,
                'name' => 'National',
            ],
        ]);
    }
}
