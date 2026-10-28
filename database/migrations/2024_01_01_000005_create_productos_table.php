<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Módulo Producto (Prendas)
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto'); // BIGSERIAL PRIMARY KEY
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->foreignId('id_categoria')->constrained('categorias', 'id_categoria')->onDelete('restrict');
            $table->decimal('precio_venta', 12, 2); // NUMERIC(12, 2)
            $table->decimal('costo_compra', 12, 2); // NUMERIC(12, 2)
            $table->integer('stock_minimo')->default(3);
            $table->string('imagen', 255)->nullable(); // Ruta o URL de la fotografía
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index('codigo');
            $table->index('id_categoria');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
