<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();

            $table->morphs('imageable');       // imageable_type, imageable_id
            $table->string('disk', 50)->default('public');
            $table->string('path', 255);       // ej: photos/students/123/abc.jpg
            $table->string('original_name', 255)->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();    // bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->string('title', 150)->nullable();
            $table->string('alt', 150)->nullable();
            $table->text('caption')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['imageable_type','imageable_id','is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
