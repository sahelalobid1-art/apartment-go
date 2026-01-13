<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run()
    {
        $banners = [
            [
                'title' => 'Welcome to Syria Stay',
                'image_url' => 'assets/banners/welcome_main.jpg',
                'link' => null,
                'is_active' => true,
                'order' => 1
            ],
            [
                'title' => 'Summer Offers in Latakia',
                'image_url' => 'assets/banners/summer_offer.jpg',
                'link' => '/search?governorate=Latakia', // رابط داخلي للبحث مثلاً
                'is_active' => true,
                'order' => 2
            ],
            [
                'title' => 'Luxury Apartments in Damascus',
                'image_url' => 'assets/banners/damascus_luxury.jpg',
                'link' => '/search?governorate=Damascus&min_price=100',
                'is_active' => true,
                'order' => 3
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(
                ['image_url' => $banner['image_url']], // نعتمد مسار الصورة كمعرف فريد لمنع التكرار
                $banner
            );
        }
    }
}
