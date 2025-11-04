<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->enum('type', ['boleta','factura'])->index();
            $table->string('folio')->nullable()->index();     // si luego integras folios
            $table->enum('status', ['pending','issued','failed'])->default('pending')->index();

            // Receptor (según tipo)
            $table->string('receiver_rut')->nullable();        // RUT empresa o contribuyente
            $table->string('receiver_name')->nullable();       // Razón social o nombre
            $table->string('receiver_giro')->nullable();       // Giro (solo factura)
            $table->string('receiver_address')->nullable();    // Dirección facturación
            $table->unsignedBigInteger('receiver_country_id')->nullable();
            $table->unsignedBigInteger('receiver_region_id')->nullable();
            $table->unsignedBigInteger('receiver_commune_id')->nullable();

            // Totales “congelados” al emitir
            $table->integer('subtotal_net')->default(0);
            $table->integer('tax_total')->default(0);
            $table->integer('grand_total')->default(0);
            $table->string('currency', 8)->default('CLP');

            // Archivos y emisión
            $table->string('pdf_path')->nullable();
            $table->timestamp('issued_at')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        // Opcional: en orders guardar preferencia
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders','doc_type')) {
                $table->enum('doc_type', ['boleta','factura'])->default('boleta')->after('payment_status');
            }
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_documents');
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders','doc_type')) {
                $table->dropColumn('doc_type');
            }
        });
    }
};
