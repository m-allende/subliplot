<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('carts', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
      $t->string('cookie_id', 100)->nullable()->index(); // para invitados
      $t->string('status', 20)->default('open');         // open|ordered|abandoned
      $t->timestamps();
      $t->unique(['user_id','status']);
      $t->unique(['cookie_id','status']);
    });

    Schema::create('cart_items', function (Blueprint $t) {
      $t->id();
      $t->uuid('row_uid')->nullable()->index();
      $t->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
      $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
      $t->unsignedInteger('qty')->default(1);
      $t->json('config_json')->nullable();    // {code:[ids], notes:"..."}
      $t->decimal('unit_price', 12, 2)->nullable(); // si luego calculas precios
      $t->decimal('line_total', 12, 2)->nullable();
      $t->timestamps();
      $t->index(['cart_id','product_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('cart_items');
    Schema::dropIfExists('carts');
  }
};
