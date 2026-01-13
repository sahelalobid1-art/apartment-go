<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | مستخدمون قيد المراجعة (Pending)
        |--------------------------------------------------------------------------
        */

        User::factory()
            ->count(5)
            ->state([
                'status' => 'pending',
            ])
            ->create();


        /*
        |--------------------------------------------------------------------------
        | مستأجرون مقبولون (Approved Tenants)
        |--------------------------------------------------------------------------
        */

        User::factory()
            ->count(5)
            ->state([
                'user_type' => 'tenant',
                'status' => 'approved',
            ])
            ->create();


        /*
        |--------------------------------------------------------------------------
        | مالكون مقبولون (Approved Owners)
        |--------------------------------------------------------------------------
        */

        User::factory()
            ->count(5)
            ->state([
                'user_type' => 'owner',
                'status' => 'approved',
            ])
            ->create();


        /*
        |--------------------------------------------------------------------------
        | مستخدم إداري تجريبي (اختياري)
        |--------------------------------------------------------------------------
        */

        User::factory()->create([
            'phone' => '0999999999',
            'user_type' => 'owner',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'status' => 'approved',
        ]);
    }
}
