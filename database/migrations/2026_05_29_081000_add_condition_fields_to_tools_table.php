<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->text('condition_notes')->nullable()->after('description');
            $table->timestamp('condition_updated_at')->nullable()->after('condition_notes');
            $table->foreignId('condition_updated_by')->nullable()->after('condition_updated_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropConstrainedForeignId('condition_updated_by');
            $table->dropColumn(['condition_notes', 'condition_updated_at']);
        });
    }
};
