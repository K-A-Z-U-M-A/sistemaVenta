<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaracteristicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caracteristicas = [
            ['nombre' => 'Marca Bebidas', 'descripcion' => 'Marca de bebidas y gaseosas', 'estado' => 1],
            ['nombre' => 'Marca Cervezas', 'descripcion' => 'Marca de cervezas', 'estado' => 1],
            ['nombre' => 'Marca Licores', 'descripcion' => 'Marca de licores y destilados', 'estado' => 1],
            ['nombre' => 'Marca Comidas', 'descripcion' => 'Marca de comidas', 'estado' => 1],
            ['nombre' => 'Presentación Botella', 'descripcion' => 'Presentaciones en botella', 'estado' => 1],
            ['nombre' => 'Presentación Copa', 'descripcion' => 'Presentaciones en copa/vaso', 'estado' => 1],
            ['nombre' => 'Presentación Unidad', 'descripcion' => 'Presentación por unidad', 'estado' => 1],
            ['nombre' => 'Categoría Comidas', 'descripcion' => 'Categorías de comidas', 'estado' => 1],
            ['nombre' => 'Categoría Bebidas', 'descripcion' => 'Categorías de bebidas', 'estado' => 1],
            ['nombre' => 'Categoría Tragos', 'descripcion' => 'Categorías de tragos', 'estado' => 1],
        ];

        foreach ($caracteristicas as $caracteristica) {
            DB::table('caracteristicas')->insert(array_merge($caracteristica, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
