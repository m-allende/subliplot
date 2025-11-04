<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->uuid('public_uid')->unique();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->uuid('cookie_id')->nullable()->index();

            // Contacto
            $t->string('buyer_name')->nullable();
            $t->string('buyer_email')->nullable();
            $t->string('buyer_phone')->nullable();
            $t->text('notes')->nullable();

            // Moneda / totales (enteros CLP)
            $t->char('currency', 3)->default('CLP');
            $t->decimal('tax_rate', 5,2)->default(19.00);

            $t->unsignedInteger('items_count')->default(0);
            $t->unsignedInteger('qty_total')->default(0);

            $t->unsignedBigInteger('subtotal_net')->default(0);
            $t->unsignedBigInteger('tax_total')->default(0);
            $t->unsignedBigInteger('grand_total')->default(0);

            // Estados
            $t->string('status', 32)->default('pending_payment');     // draft|pending_payment|paid|processing|shipped|completed|canceled
            $t->string('payment_status', 32)->default('unpaid');      // unpaid|partial|paid|refunded

            $t->json('meta_json')->nullable();

            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
