<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');

            // Atributos posibles (solo algunos serán usados según flags del producto)
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('paper_id')->nullable();
            $table->unsignedBigInteger('bleed_id')->nullable();
            $table->unsignedBigInteger('finish_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedBigInteger('shape_id')->nullable();
            $table->unsignedBigInteger('print_side_id')->nullable();
            $table->unsignedBigInteger('mounting_id')->nullable();
            $table->unsignedBigInteger('rolling_id')->nullable();
            $table->unsignedBigInteger('hole_id')->nullable();
            $table->unsignedBigInteger('quantity_id')->nullable();

            $table->decimal('price', 15, 4)->default(0);

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
