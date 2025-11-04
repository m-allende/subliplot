<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_attribute_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('attribute_type_id')->constrained('attribute_types')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->boolean('required')->default(false);
            $table->boolean('multi_select')->default(false);
            $table->string('show_as', 20)->default('select'); // select|chips|radio
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_id','attribute_type_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_attribute_settings');
    }
};
