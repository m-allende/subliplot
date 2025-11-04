<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_type_id')->constrained('attribute_types')->cascadeOnDelete();
            $table->string('name');                          // visible (ES)
            $table->string('code', 80)->nullable();          // corto/slug interno opcional
            // campos genéricos útiles
            $table->unsignedInteger('width_mm')->nullable();  // para tamaños
            $table->unsignedInteger('height_mm')->nullable(); // para tamaños
            $table->unsignedSmallInteger('weight_gsm')->nullable(); // para papeles
            $table->string('color_hex', 7)->nullable();       // #RRGGBB si aplica
            $table->json('extra_json')->nullable();           // metadatos flexibles
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['attribute_type_id', 'active']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('attribute_values');
    }
};
