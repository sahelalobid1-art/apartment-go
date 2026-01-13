<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run()
    {
        $amenities = [
            ['name' => 'Wi-Fi', 'icon' => 'fa-wifi'],
            ['name' => 'Swimming Pool', 'icon' => 'fa-swimming-pool'],
            ['name' => 'Parking', 'icon' => 'fa-parking'],
            ['name' => 'Air Conditioning', 'icon' => 'fa-snowflake'],
            ['name' => 'Tv', 'icon' => 'tv'],
            ['name' => 'Football', 'icon' => 'sports_soccer'],
            ['name' => 'Bascketball', 'icon' => 'sports_basketball'],
            ['name' => 'Tennis', 'icon' => 'sports_tennis'],

        ];

        foreach ($amenities as $item) {
             // نستخدم updateOrCreate لتحديث القيم القديمة بالروابط الجديدة
            Amenity::updateOrCreate(
                ['name' => $item['name']],
                ['icon' => $item['icon']]
            );
        }
    }
}
