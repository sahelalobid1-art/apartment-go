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
        Schema::create('apartment_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->unique(['apartment_id', 'amenity_id']);
        });

        $apartments = Apartment::all();
        foreach ($apartments as $apartment) {
            $amenitiesJson = $apartment->getRawOriginal('amenities');

            if ($amenitiesJson) {
                $amenitiesIds = json_decode($amenitiesJson, true);

                if (is_array($amenitiesIds) && count($amenitiesIds) > 0) {
                    try {
                        $apartment->amenities()->syncWithoutDetaching($amenitiesIds);
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('amenities');
        });
    }

    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->json('amenities')->nullable();
        });
        Schema::dropIfExists('apartment_amenity');
    }
};
