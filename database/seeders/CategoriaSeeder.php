<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Super Panchos', 'descripcion' => 'Diferentes tipos de super panchos'],
            ['nombre' => 'Bebidas Gaseosas', 'descripcion' => 'Gaseosas y refrescos'],
            ['nombre' => 'Cervezas', 'descripcion' => 'Cervezas nacionales e importadas'],
            ['nombre' => 'Tragos Clásicos', 'descripcion' => 'Cocteles y tragos preparados'],
            ['nombre' => 'Shots', 'descripcion' => 'Shots de licor'],
        ];

        foreach ($categorias as $categoria) {
            // Crear la característica primero
            $caracteristicaId = DB::table('caracteristicas')->insertGetId([
                'nombre' => $categoria['nombre'],
                'descripcion' => $categoria['descripcion'],
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Crear la categoría con su característica
            DB::table('categorias')->insert([
                'caracteristica_id' => $caracteristicaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
