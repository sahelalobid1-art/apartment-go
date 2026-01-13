<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description');
            $table->string('governorate', 100);
            $table->string('city', 100);
            $table->text('address');
            $table->decimal('price_per_night', 10, 2);
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->decimal('area', 8, 2);
            $table->integer('max_guests');
            $table->json('amenities')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();

            $table->index(['governorate', 'city']);
            $table->index('price_per_night');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartments');
    }
};
