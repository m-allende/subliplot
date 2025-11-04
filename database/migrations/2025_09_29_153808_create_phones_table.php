<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('phones', function (Blueprint $t) {
            $t->id();
            $t->morphs('phoneable');
            $t->enum('kind', ['mobile','home','work'])->default('mobile');
            $t->string('country_code', 5)->default('+56');
            $t->string('number', 30);
            $t->boolean('is_default')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('phones');
    }
};
