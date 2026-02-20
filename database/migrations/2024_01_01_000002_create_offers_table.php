<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->string('image_url')->nullable();
            $table->integer('max_bookings_per_day')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
