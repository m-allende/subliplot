<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $t->string('method')->nullable();  // webpay|transfer|...
            $t->unsignedBigInteger('amount')->default(0);
            $t->char('currency', 3)->default('CLP');
            $t->string('status', 32)->default('initiated'); // initiated|authorized|paid|failed|refunded
            $t->string('processor_id')->nullable();
            $t->json('payload_json')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
