<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->default(0)->after('daily_rate');
            $table->decimal('discount', 10, 2)->default(0)->after('deposit');
            $table->decimal('late_fee', 10, 2)->default(0)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['deposit', 'discount', 'late_fee']);
        });
    }
};
