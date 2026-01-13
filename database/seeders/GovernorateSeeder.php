<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run()
    {
        $governorates = [
            ['name' => 'Damascus', 'image_url' => 'assets/governorates/damascus.jpg'],
            ['name' => 'Aleppo', 'image_url' => 'assets/governorates/aleppo.jpg'],
            ['name' => 'Homs', 'image_url' => 'assets/governorates/homs.jpg'],
            ['name' => 'Hama', 'image_url' => 'assets/governorates/hama.jpg'],
            ['name' => 'Latakia', 'image_url' => 'assets/governorates/latakia.jpg'],
            ['name' => 'Tartus', 'image_url' => 'assets/governorates/tartus.jpg'],
            ['name' => 'Idlib', 'image_url' => 'assets/governorates/idlib.jpg'],
            ['name' => 'Daraa', 'image_url' => 'assets/governorates/daraa.jpg'],
            ['name' => 'Deir ez-Zor', 'image_url' => 'assets/governorates/deir_ezor.jpg'],

        ];

        foreach ($governorates as $gov) {
            Governorate::firstOrCreate(['name' => $gov['name']], $gov);
        }
    }
}
