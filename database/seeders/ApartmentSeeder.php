<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Amenity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // استخدام واجهة Http بدلاً من file_get_contents
use Illuminate\Support\Str;

class ApartmentSeeder extends Seeder
{
    public function run()
    {
        // 1. تجهيز مجلد الصور
        Storage::disk('public')->makeDirectory('apartments');

        // 2. جلب مالك ومرافق
        $owner = User::first() ?? User::factory()->create();
        $amenities = Amenity::all();

        // 3. إنشاء 5 شقق
        for ($i = 1; $i <= 5; $i++) {
            $this->command->info("Creating Apartment #$i...");

            $apartment = Apartment::create([
                'owner_id' => $owner->id,
                'title' => "Modern Apartment #$i in Syria",
                'description' => "Enjoy a comfortable stay in this fully furnished apartment located in a prime area.",
                'governorate' => $this->getRandomGovernorate(),
                'city' => 'City Center',
                'address' => 'Main Street, Building ' . rand(1, 50),
                'price_per_night' => rand(50, 300),
                'bedrooms' => rand(1, 4),
                'bathrooms' => rand(1, 3),
                'area' => rand(80, 250),
                'max_guests' => rand(2, 8),
                'status' => 'available',
                'average_rating' => rand(3, 5), // تقييم مبدئي
            ]);

            // ربط المرافق (Pivot Table)
            if ($amenities->count() > 0) {
                // نأخذ عدد عشوائي من المرافق ونربطها بالشقة
                $apartment->amenities()->attach($amenities->random(rand(3, 6))->pluck('id'));
            }

            // 4. تحميل وحفظ الصورة
            $imageName = 'apartments/' . Str::random(15) . '.jpg';

            try {
                // نستخدم Picsum وهي خدمة مستقرة وسريعة
                // نستخدم Http Facade لأنه أفضل في التعامل مع الأخطاء من file_get_contents
                $response = Http::timeout(10)->get('https://picsum.photos/800/600');

                if ($response->successful()) {
                    Storage::disk('public')->put($imageName, $response->body());

                    // حفظ السجل في قاعدة البيانات
                    $apartment->images()->create([
                        'image_url' => $imageName,
                        'is_primary' => true
                    ]);

                    $this->command->info(" - Image downloaded successfully.");
                } else {
                    $this->command->warn(" - Failed to download image (HTTP Error).");
                }

            } catch (\Exception $e) {
                // في حال انقطاع النت، لا نوقف العملية، بل نكمل بدون صورة
                $this->command->error(" - Could not download image: " . $e->getMessage());
            }
        }
    }

    private function getRandomGovernorate()
    {
        $govs = ['Damascus', 'Aleppo', 'Latakia', 'Homs', 'Tartus'];
        return $govs[array_rand($govs)];
    }
}
