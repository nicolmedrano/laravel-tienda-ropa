<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Módulo Venta
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->string('numero_factura', 50)->unique();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes', 'id_cliente')->onDelete('set null');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('restrict');
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->string('estado', 20)->default('completada'); // 'completada' o 'anulada'
            $table->timestamps();

            $table->index('id_cliente');
            $table->index('id_usuario');
            $table->index('fecha_venta');
            $table->index('numero_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
