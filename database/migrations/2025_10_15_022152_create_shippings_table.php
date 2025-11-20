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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('service_type');
            $table->decimal('base_rate', 10, 2);
            $table->decimal('per_kg', 10, 2)->default(0);
            $table->unsignedTinyInteger('min_delivery_days')->nullable();
            $table->unsignedTinyInteger('max_delivery_days')->nullable();
            $table->string('coverage')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->string('tracking_number')->unique()->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('estimated_arrival_date')->nullable();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->datetime('actual_arrival')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
