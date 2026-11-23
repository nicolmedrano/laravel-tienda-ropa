<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Campos extendidos para perfil completo de Cliente en Bolivia
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('tipo_cliente', 20)->default('persona')->after('id_cliente'); // persona, empresa
            $table->string('ci_nit', 30)->nullable()->after('apellido');
            $table->date('fecha_nacimiento')->nullable()->after('ci_nit');
            $table->string('telefono_alternativo', 30)->nullable()->after('telefono');
            $table->string('canal_preferido', 30)->default('whatsapp')->after('email'); // whatsapp, llamada, correo
            $table->string('origen', 50)->default('tienda_fisica')->after('canal_preferido'); // facebook, instagram, tiktok, whatsapp, referido, tienda_fisica, otro
            $table->json('etiquetas')->nullable()->after('origen'); // ['VIP', 'frecuente', 'mayorista', 'nuevo']
            $table->text('notas')->nullable()->after('etiquetas');

            $table->index('ci_nit');
            $table->index('origen');
            $table->index('tipo_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['ci_nit']);
            $table->dropIndex(['origen']);
            $table->dropIndex(['tipo_cliente']);
            $table->dropColumn([
                'tipo_cliente',
                'ci_nit',
                'fecha_nacimiento',
                'telefono_alternativo',
                'canal_preferido',
                'origen',
                'etiquetas',
                'notas',
            ]);
        });
    }
};
