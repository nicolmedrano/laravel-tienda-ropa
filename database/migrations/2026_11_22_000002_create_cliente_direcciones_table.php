<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Múltiples direcciones por Cliente
     */
    public function up(): void
    {
        Schema::create('cliente_direcciones', function (Blueprint $table) {
            $table->id('id_direccion');
            $table->unsignedBigInteger('id_cliente');
            $table->string('alias', 50)->default('Casa'); // Casa, Trabajo, Otra
            $table->string('ciudad', 100)->default('Santa Cruz de la Sierra');
            $table->string('zona', 150)->nullable();
            $table->string('calle', 200)->nullable();
            $table->text('referencia')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->foreign('id_cliente')
                  ->references('id_cliente')
                  ->on('clientes')
                  ->onDelete('cascade');

            $table->index('id_cliente');
            $table->index(['id_cliente', 'es_principal']);
        });

        // Migración de datos existentes: si un cliente ya tenía dirección, registrarla como principal
        try {
            $clientesConDireccion = DB::table('clientes')
                ->whereNotNull('direccion')
                ->where('direccion', '<>', '')
                ->get();

            foreach ($clientesConDireccion as $cli) {
                DB::table('cliente_direcciones')->insert([
                    'id_cliente' => $cli->id_cliente,
                    'alias' => 'Principal',
                    'ciudad' => 'Santa Cruz de la Sierra',
                    'zona' => null,
                    'calle' => $cli->direccion,
                    'referencia' => null,
                    'es_principal' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Continuar sin interrumpir la creación de la tabla si no hay registros
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_direcciones');
    }
};
