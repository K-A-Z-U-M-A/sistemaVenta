<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // COMIDAS - Super Panchos
        $productos = [
            // SUPER PANCHOS (Comida)
            [
                'codigo' => 'SP001',
                'nombre' => 'Super Pancho Clásico',
                'tipo_producto' => 'comida',
                'aplica_descuento_trago' => false,
                'stock' => 50,
                'descripcion' => 'Super pancho con salchichas, queso, huevo y salsas',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 7, // Casa Propia
                'presentacione_id' => 1, // Unidad
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SP002',
                'nombre' => 'Super Pancho Completo',
                'tipo_producto' => 'comida',
                'aplica_descuento_trago' => false,
                'stock' => 50,
                'descripcion' => 'Super pancho con doble salchicha, queso, huevo, jamón y todas las salsas',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 7,
                'presentacione_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SP003',
                'nombre' => 'Super Pancho Especial',
                'tipo_producto' => 'comida',
                'aplica_descuento_trago' => false,
                'stock' => 50,
                'descripcion' => 'Super pancho con tocino, queso cheddar, cebolla caramelizada',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 7,
                'presentacione_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SP004',
                'nombre' => 'Super Pancho Vegetariano',
                'tipo_producto' => 'comida',
                'aplica_descuento_trago' => false,
                'stock' => 30,
                'descripcion' => 'Super pancho con salchicha vegetal, vegetales grillados y guacamole',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 7,
                'presentacione_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SP005',
                'nombre' => 'Super Pancho Picante',
                'tipo_producto' => 'comida',
                'aplica_descuento_trago' => false,
                'stock' => 40,
                'descripcion' => 'Super pancho con salchicha picante, jalapeños, salsa picante',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 7,
                'presentacione_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // BEBIDAS
            [
                'codigo' => 'CC001',
                'nombre' => 'Coca Cola',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 100,
                'descripcion' => 'Coca Cola 350ml',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 1, // Coca Cola
                'presentacione_id' => 2, // 350ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'CC002',
                'nombre' => 'Coca Cola',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 80,
                'descripcion' => 'Coca Cola 1.5L',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 1,
                'presentacione_id' => 5, // 1.5L
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'CC003',
                'nombre' => 'Coca Cola',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 60,
                'descripcion' => 'Coca Cola 2L',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 1,
                'presentacione_id' => 6, // 2L
                'created_at' => now(),
                'updated_at' => now()
            ],

            // CERVEZAS
            [
                'codigo' => 'BR001',
                'nombre' => 'Brahma Chopp',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 120,
                'descripcion' => 'Cerveza Brahma 350ml',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 2, // Brahma
                'presentacione_id' => 2, // 350ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'PIL001',
                'nombre' => 'Pilsen',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 100,
                'descripcion' => 'Cerveza Pilsen 350ml',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 3, // Pilsen
                'presentacione_id' => 2, // 350ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'BR002',
                'nombre' => 'Brahma Chopp Litro',
                'tipo_producto' => 'bebida',
                'aplica_descuento_trago' => false,
                'stock' => 50,
                'descripcion' => 'Cerveza Brahma 1L',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 2,
                'presentacione_id' => 4, // 1L
                'created_at' => now(),
                'updated_at' => now()
            ],

            // TRAGOS CLÁSICOS (Con descuento de 5000 Gs)
            [
                'codigo' => 'TR001',
                'nombre' => 'Caipiriña',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => true, // SÍ aplica descuento
                'stock' => 0, // Los tragos se preparan al momento
                'descripcion' => 'Caipiriña clásica con cachaça, limón y azúcar',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 5, // Cachaça 51
                'presentacione_id' => 8, // Copa 200ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'TR002',
                'nombre' => 'Caipiruba',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => true, // SÍ aplica descuento
                'stock' => 0,
                'descripcion' => 'Caipiruba con vodka, limón y azúcar',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 4, // Smirnoff
                'presentacione_id' => 8, // Copa 200ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'TR003',
                'nombre' => 'Tequila Sunrise',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => true, // SÍ aplica descuento
                'stock' => 0,
                'descripcion' => 'Tequila con jugo de naranja y granadina',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 6, // Jose Cuervo
                'presentacione_id' => 8, // Copa 200ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'TR004',
                'nombre' => 'Margarita',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => true, // SÍ aplica descuento
                'stock' => 0,
                'descripcion' => 'Margarita clásica con tequila y limón',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 6, // Jose Cuervo
                'presentacione_id' => 8, // Copa 200ml
                'created_at' => now(),
                'updated_at' => now()
            ],

            // SHOTS (SIN descuento de 5000 Gs)
            [
                'codigo' => 'SH001',
                'nombre' => 'Shot de Tequila',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => false, // NO aplica descuento
                'stock' => 0,
                'descripcion' => 'Shot de tequila puro',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 6, // Jose Cuervo
                'presentacione_id' => 7, // Shot 50ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SH002',
                'nombre' => 'Shot de Vodka',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => false, // NO aplica descuento
                'stock' => 0,
                'descripcion' => 'Shot de vodka puro',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 4, // Smirnoff
                'presentacione_id' => 7, // Shot 50ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SH003',
                'nombre' => 'Shot de Cachaça',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => false, // NO aplica descuento
                'stock' => 0,
                'descripcion' => 'Shot de cachaça pura',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 5, // Cachaça 51
                'presentacione_id' => 7, // Shot 50ml
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'SH004',
                'nombre' => 'Jägermeister Shot',
                'tipo_producto' => 'trago',
                'aplica_descuento_trago' => false, // NO aplica descuento
                'stock' => 0,
                'descripcion' => 'Shot de Jägermeister',
                'fecha_vencimiento' => null,
                'img_path' => null,
                'estado' => 1,
                'marca_id' => 4, // Smirnoff (temporal)
                'presentacione_id' => 7, // Shot 50ml
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($productos as $producto) {
            $productoId = DB::table('productos')->insertGetId($producto);
            
            // Asignar categoría según el tipo
            $categoriaId = match($producto['codigo'][0] . $producto['codigo'][1]) {
                'SP' => 1, // Super Panchos
                'CC' => 2, // Bebidas Gaseosas
                'BR', 'PI' => 3, // Cervezas
                'TR' => 4, // Tragos Clásicos
                'SH' => 5, // Shots
                default => 1
            };
            
            DB::table('categoria_producto')->insert([
                'categoria_id' => $categoriaId,
                'producto_id' => $productoId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
