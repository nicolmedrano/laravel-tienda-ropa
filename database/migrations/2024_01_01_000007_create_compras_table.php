<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Módulo Compra
     */
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id('id_compra');
            $table->string('numero_comprobante', 50)->unique();
            $table->foreignId('id_proveedor')->constrained('proveedores', 'id_proveedor')->onDelete('restrict');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('restrict');
            $table->timestamp('fecha_compra')->useCurrent();
            $table->decimal('total', 12, 2); // NUMERIC(12, 2)
            $table->string('estado', 20)->default('completada'); // 'completada' o 'anulada'
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('id_proveedor');
            $table->index('id_usuario');
            $table->index('fecha_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
