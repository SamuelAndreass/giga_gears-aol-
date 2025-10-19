<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_store_id')->constrained('seller_stores')->onDelete('cascade');
            $table->decimal('original_price', 10, 2); $table->decimal('discount_price', 10, 2);
            $table->integer('discount_percentage')->default(0); $table->json('images')->nullable();
            $table->json('colors')->nullable(); $table->json('specifications')->nullable();
            $table->json('features')->nullable(); $table->integer('stock')->default(0);
            $table->string('brand'); $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0); $table->boolean('is_featured')->default(false);
            $table->timestamps(); $table->index(['category_id', 'is_featured']);
        });
    }
    public function down() { Schema::dropIfExists('products'); }
};