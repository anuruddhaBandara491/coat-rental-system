<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addOrderStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_status')->insert([
            [
                'name' => 'Pending',
                'description' => 'Coats ready for pickup',
            ],
            [
                'name' => 'IN_USE',
                'description' => 'Coats are with customer',
            ],
            [
                'name' => 'RETURNED',
                'description' => 'Coats returned to inventory',
            ],
            [
                'name' => 'CANCELLED',
                'description' => 'Order cancelled',
            ],
        ]);
    }
}
