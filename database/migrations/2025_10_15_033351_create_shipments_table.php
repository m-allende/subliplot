<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $t->string('carrier')->nullable();
            $t->string('service')->nullable();
            $t->string('tracking_code')->nullable();

            $t->unsignedBigInteger('shipping_cost_gross')->default(0);
            $t->unsignedBigInteger('shipping_cost_net')->default(0);
            $t->unsignedBigInteger('shipping_tax')->default(0);

            $t->string('status', 32)->default('pending'); // pending|in_transit|delivered|returned
            $t->json('labels_json')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};
