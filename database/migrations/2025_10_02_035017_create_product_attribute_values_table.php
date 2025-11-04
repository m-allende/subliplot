<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();

            $table->boolean('is_default')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Pricing opcional (por si mañana lo usas)
            $table->enum('price_delta_type', ['fixed','percent'])->nullable();
            $table->decimal('price_delta', 10, 2)->nullable();
            $table->json('extra_json')->nullable();

            $table->timestamps();

            $table->unique(['product_id','attribute_value_id']);
            $table->index(['product_id','is_default']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_attribute_values');
    }
};
