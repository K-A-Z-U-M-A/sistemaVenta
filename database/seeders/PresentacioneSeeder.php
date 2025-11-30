<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentacioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentaciones = [
            ['nombre' => 'Unidad', 'descripcion' => 'Unidad individual'],
            ['nombre' => '350ml', 'descripcion' => 'Botella 350ml'],
            ['nombre' => '500ml', 'descripcion' => 'Botella 500ml'],
            ['nombre' => '1L', 'descripcion' => 'Botella 1 litro'],
            ['nombre' => '1.5L', 'descripcion' => 'Botella 1.5 litros'],
            ['nombre' => '2L', 'descripcion' => 'Botella 2 litros'],
            ['nombre' => 'Shot 50ml', 'descripcion' => 'Shot 50ml'],
            ['nombre' => 'Copa 200ml', 'descripcion' => 'Copa 200ml'],
        ];

        foreach ($presentaciones as $presentacion) {
            // Crear la característica primero
            $caracteristicaId = DB::table('caracteristicas')->insertGetId([
                'nombre' => $presentacion['nombre'],
                'descripcion' => $presentacion['descripcion'],
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Crear la presentación con su característica
            DB::table('presentaciones')->insert([
                'caracteristica_id' => $caracteristicaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
