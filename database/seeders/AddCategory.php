<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class AddCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemCategory::insert([
            [
                'id' => 1,
                'name' => 'Coat',
            ],
            [
                'id' => 2,
                'name' => 'Trouser',
            ],
            [
                'id' => 3,
                'name' => 'West',
            ],
        ]);
    }
}
