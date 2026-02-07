<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class addUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'first_name' => 'sanjaya',
                'last_name' => 'tailor',
                'email' => 'sanjaya@gmail.com',
                'password' => Hash::make('123456'),
            ],
        ]);
    }
}
