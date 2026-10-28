<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Módulo Usuario
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // BIGSERIAL PRIMARY KEY en PostgreSQL
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('usuario', 50)->unique();
            $table->string('password', 255);
            $table->string('rol', 30)->default('vendedor'); // 'administrador' o 'vendedor'
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index('usuario');
            $table->index('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
