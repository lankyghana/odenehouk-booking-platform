<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unique('booking_id', 'payments_booking_id_unique');
            $table->index('booking_id', 'payments_booking_id_idx');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('offer_id', 'bookings_offer_id_idx');
            $table->index('booking_date', 'bookings_booking_date_idx');
            $table->index(['offer_id', 'booking_date', 'status'], 'bookings_offer_date_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_booking_id_unique');
            $table->dropIndex('payments_booking_id_idx');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_offer_id_idx');
            $table->dropIndex('bookings_booking_date_idx');
            $table->dropIndex('bookings_offer_date_status_idx');
        });
    }
};
