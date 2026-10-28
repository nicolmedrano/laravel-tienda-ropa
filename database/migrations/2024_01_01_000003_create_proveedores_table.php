<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Módulo Proveedor
     */
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id('id_proveedor'); // BIGSERIAL PRIMARY KEY
            $table->string('nombre_empresa', 150);
            $table->string('contacto', 100);
            $table->string('telefono', 30);
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index('nombre_empresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
