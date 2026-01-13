<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Apartment;

return new class extends Migration
{
    public function up()
    {
        // 1. إنشاء الجدول الوسيط
        Schema::create('apartment_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            // منع تكرار نفس المرفق لنفس الشقة
            $table->unique(['apartment_id', 'amenity_id']);
        });

        // 2. نقل البيانات القديمة (من JSON إلى Pivot)
        $apartments = Apartment::all();
        foreach ($apartments as $apartment) {
            // نستخدم getRawOriginal لجلب البيانات الخام قبل أن يتدخل الـ Cast
            $amenitiesJson = $apartment->getRawOriginal('amenities');

            if ($amenitiesJson) {
                $amenitiesIds = json_decode($amenitiesJson, true);

                if (is_array($amenitiesIds) && count($amenitiesIds) > 0) {
                    // إدخال البيانات في الجدول الجديد
                    // نستخدم try/catch لتجنب الأخطاء إذا كان هناك IDs غير صالحة
                    try {
                        $apartment->amenities()->syncWithoutDetaching($amenitiesIds);
                    } catch (\Exception $e) {
                        // تجاهل الأخطاء أثناء النقل
                    }
                }
            }
        }

        // 3. حذف العمود القديم (اختياري، يفضل حذفه لتنظيف الجدول)
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('amenities');
        });
    }

    public function down()
    {
        // استعادة العمود القديم (في حال التراجع)
        Schema::table('apartments', function (Blueprint $table) {
            $table->json('amenities')->nullable();
        });
        Schema::dropIfExists('apartment_amenity');
    }
};
