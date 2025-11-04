<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Referencias/snapshot
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('product_name');
            $t->string('product_thumb')->nullable();

            // Cantidades
            $t->boolean('uses_quantity')->default(false);
            $t->unsignedInteger('qty_raw')->default(1);          // lo digitado o id de quantity
            $t->string('qty_display')->nullable();               // "100 unidades"
            $t->unsignedInteger('qty_real')->default(1);         // numérico para cálculos

            // Precios (CLP, enteros)
            $t->unsignedBigInteger('unit_price_gross')->default(0);
            $t->unsignedBigInteger('unit_price_net')->default(0);
            $t->unsignedBigInteger('tax_amount_unit')->default(0);

            $t->unsignedBigInteger('line_total_gross')->default(0);
            $t->unsignedBigInteger('line_total_net')->default(0);
            $t->unsignedBigInteger('line_tax_total')->default(0);

            // Opciones elegidas
            $t->json('options_json')->nullable();      // { code: [ids] }
            $t->json('options_display')->nullable();   // [{group, value}]
            $t->json('options_map')->nullable();       // auxiliares (p.ej. quantity_id)

            // Archivo (opcional)
            $t->string('file_disk')->nullable();
            $t->string('file_path')->nullable();
            $t->string('file_name')->nullable();
            $t->unsignedBigInteger('file_size')->nullable();

            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_items');
    }
};
