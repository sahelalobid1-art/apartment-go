<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        User::factory()->create([
            'phone' => '+11111111111',
            'email' => 'sahlalobaid@gmail.com',
            'password' => Hash::make('123456'),
            'user_type' => 'admin',
            'first_name' => 'Sahl',
            'last_name' => 'Al-Obaid',
            'status' => 'approved',
        ]);
    }
}
