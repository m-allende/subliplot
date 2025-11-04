<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id')->index();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();

            // Flags mantenibles (PDF): marca qué configuraciones usa el producto
            $table->boolean('uses_size')->default(false);         // Tamaños
            $table->boolean('uses_paper')->default(false);        // Tipo de papel
            $table->boolean('uses_bleed')->default(false);        // Corte excedente
            $table->boolean('uses_finish')->default(false);       // Acabados (laminado, barniz, etc.)
            $table->boolean('uses_material')->default(false);     // Material (vinilo, PVC, tela, etc.)
            $table->boolean('uses_shape')->default(false);        // Formas (cuadrado, circular, irregular)
            $table->boolean('uses_print_side')->default(false);   // 1/2 caras
            $table->boolean('uses_mounting')->default(false);     // Montaje / bastidor / base
            $table->boolean('uses_rolling')->default(false);      // En rollo (stickers)
            $table->boolean('uses_holes')->default(false);        // Ojetillos / perforaciones

            // Económicos
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(0)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
