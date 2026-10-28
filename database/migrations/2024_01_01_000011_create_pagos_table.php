<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Registro de Pagos
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->foreignId('id_venta')->constrained('ventas', 'id_venta')->onDelete('cascade');
            $table->decimal('monto', 12, 2);
            $table->string('metodo', 30); // 'efectivo', 'tarjeta', 'transferencia', 'qr'
            $table->string('referencia', 100)->nullable();
            $table->string('estado', 20)->default('completado'); // 'completado', 'pendiente', 'reembolsado'
            $table->timestamp('fecha_pago')->useCurrent();
            $table->timestamps();

            $table->index('id_venta');
            $table->index('metodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
