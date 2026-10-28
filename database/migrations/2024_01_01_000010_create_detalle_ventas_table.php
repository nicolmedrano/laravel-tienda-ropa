<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Detalle de Ventas
     */
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id('id_detalle_venta');
            $table->foreignId('id_venta')->constrained('ventas', 'id_venta')->onDelete('cascade');
            $table->foreignId('id_variante')->constrained('variantes_producto', 'id_variante')->onDelete('restrict');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->index('id_venta');
            $table->index('id_variante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
