<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_addresses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $t->enum('type', ['billing','shipping'])->default('shipping');

            $t->string('line1');
            $t->string('line2')->nullable();
            $t->string('reference')->nullable();

            $t->unsignedBigInteger('country_id')->nullable();
            $t->unsignedBigInteger('region_id')->nullable();
            $t->unsignedBigInteger('commune_id')->nullable();

            $t->string('country_name')->nullable();
            $t->string('region_name')->nullable();
            $t->string('commune_name')->nullable();

            $t->string('postal_code')->nullable();
            $t->decimal('latitude', 11,8)->nullable();
            $t->decimal('longitude', 11,8)->nullable();

            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_addresses');
    }
};
