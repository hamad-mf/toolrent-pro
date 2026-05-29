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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->default('#0d6efd'); // Bootstrap primary
            $table->string('secondary_color')->default('#6c757d');
            $table->string('system_name')->default('ToolRent Pro');
            $table->text('custom_css')->nullable();
            $table->string('plan')->default('Basic');
            $table->integer('max_users')->default(5);
            $table->integer(column: 'max_tools')->default(50);
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable(); // Feature Flags
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
