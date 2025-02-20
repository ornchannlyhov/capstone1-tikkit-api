<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create an admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'phone_number' => '0964141037',
            'password' => Hash::make('super_admin123'),
            'role' => 'admin',
            'provider' => null,
            'provider_id' => null,
        ]);
    }
}
