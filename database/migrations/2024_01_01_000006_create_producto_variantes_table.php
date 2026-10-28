<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Variantes de Producto (Talla, Color, Stock)
     */
    public function up(): void
    {
        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->id('id_variante');
            $table->foreignId('id_producto')->constrained('productos', 'id_producto')->onDelete('cascade');
            $table->string('codigo_sku', 60)->unique();
            $table->string('talla', 10);
            $table->string('color', 50);
            $table->integer('stock_actual')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_producto', 'talla', 'color'], 'uq_producto_talla_color');
            $table->index('id_producto');
            $table->index('stock_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes_producto');
    }
};
