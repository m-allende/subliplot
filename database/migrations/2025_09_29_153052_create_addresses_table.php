<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // Polimórfica
            $table->morphs('addressable'); // addressable_type, addressable_id (indexados)

            // Campos de dirección (flexibles)
            $table->string('line1')->nullable();      // calle y número
            $table->string('line2')->nullable();      // depto/oficina/extra
            $table->string('reference')->nullable();  // referencias

            // División política (Chile)
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('commune_id')->nullable();
            $table->string('postal_code', 20)->nullable();

            // Geolocalización
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Principal?
            $table->boolean('is_primary')->default(true);

            $table->softDeletes();
            $table->timestamps();

            // FKs suaves (allow null mientras migramos datos)
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('region_id')->references('id')->on('regions')->nullOnDelete();
            $table->foreign('commune_id')->references('id')->on('communes')->nullOnDelete();

            $table->index(['region_id','commune_id','is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
