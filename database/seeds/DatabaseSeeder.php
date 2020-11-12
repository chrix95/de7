<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // customer role = 0
        // driver role = 1
        // admin role = 2
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '08183780409',
            'password' => Hash::make('secret'),
            'role' => 2
        ]);        
        User::create([
            'name' => 'Driver',
            'email' => 'driver@test.com',
            'phone' => '08183780408',
            'password' => Hash::make('secret'),
            'role' => 1
        ]);        
        User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'phone' => '09057041663',
            'password' => Hash::make('secret'),
            'role' => 0
        ]);
    }
}
