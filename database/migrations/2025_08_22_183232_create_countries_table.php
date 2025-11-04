<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('iso2', 2)->unique();  // "CL"
            $table->char('iso3', 3)->unique()->nullable(); // "CHL"
            $table->string('phone_code', 6)->nullable();   // "+56"
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('code', 10)->nullable();  // ej: "RM"
            $table->string('name');                  // "Región Metropolitana de Santiago"
            $table->unsignedSmallInteger('ordinal')->nullable(); // 1..16 (opcional)
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->unique(['country_id','name']);
            $table->index(['country_id','code']);
        });

        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->string('name');                  // "Santiago"
            $table->string('code', 10)->nullable();  // código territorial (opcional)
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->cascadeOnDelete();
            $table->unique(['region_id','name']);
            $table->index(['region_id','code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('countries');
    }
};
