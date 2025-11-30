<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Coca Cola', 'descripcion' => 'Bebidas'],
            ['nombre' => 'Brahma', 'descripcion' => 'Cervezas'],
            ['nombre' => 'Pilsen', 'descripcion' => 'Cervezas'],
            ['nombre' => 'Smirnoff', 'descripcion' => 'Licores'],
            ['nombre' => 'Cachaça 51', 'descripcion' => 'Licores'],
            ['nombre' => 'Jose Cuervo', 'descripcion' => 'Tequila'],
            ['nombre' => 'Casa Propia', 'descripcion' => 'Comidas'],
        ];

        foreach ($marcas as $marca) {
            // Crear la característica primero
            $caracteristicaId = DB::table('caracteristicas')->insertGetId([
                'nombre' => $marca['nombre'],
                'descripcion' => $marca['descripcion'],
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Crear la marca con su característica
            DB::table('marcas')->insert([
                'caracteristica_id' => $caracteristicaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
