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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('tool_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Staff member who processed it
            
            $table->timestamp('checkout_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('total_price', 10, 2)->nullable();
            
            $table->string('status')->default('Active'); // Active, Returned, Overdue
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
