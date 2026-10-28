<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database in PostgreSQL.
     */
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
            CompraSeeder::class,
            VentaSeeder::class,
        ]);
    }
}
