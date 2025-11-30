<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrecioProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar precios de Super Panchos
        DB::table('productos')->where('codigo', 'SP001')->update(['precio_venta' => 25000]); // Clásico
        DB::table('productos')->where('codigo', 'SP002')->update(['precio_venta' => 35000]); // Completo
        DB::table('productos')->where('codigo', 'SP003')->update(['precio_venta' => 32000]); // Especial
        DB::table('productos')->where('codigo', 'SP004')->update(['precio_venta' => 30000]); // Vegetariano
        DB::table('productos')->where('codigo', 'SP005')->update(['precio_venta' => 28000]); // Picante

        // Actualizar precios de Bebidas
        DB::table('productos')->where('codigo', 'CC001')->update(['precio_venta' => 8000]);   // Coca 350ml
        DB::table('productos')->where('codigo', 'CC002')->update(['precio_venta' => 15000]);  // Coca 1.5L
        DB::table('productos')->where('codigo', 'CC003')->update(['precio_venta' => 18000]);  // Coca 2L

        // Actualizar precios de Cervezas
        DB::table('productos')->where('codigo', 'BR001')->update(['precio_venta' => 12000]);  // Brahma 350ml
        DB::table('productos')->where('codigo', 'PIL001')->update(['precio_venta' => 11000]); // Pilsen 350ml
        DB::table('productos')->where('codigo', 'BR002')->update(['precio_venta' => 25000]);  // Brahma 1L

        // Actualizar precios de Tragos Clásicos (con descuento de 5000)
        DB::table('productos')->where('codigo', 'TR001')->update(['precio_venta' => 35000]);  // Caipiriña
        DB::table('productos')->where('codigo', 'TR002')->update(['precio_venta' => 35000]);  // Caipiruba
        DB::table('productos')->where('codigo', 'TR003')->update(['precio_venta' => 40000]);  // Tequila Sunrise
        DB::table('productos')->where('codigo', 'TR004')->update(['precio_venta' => 38000]);  // Margarita

        // Actualizar precios de Shots (sin descuento)
        DB::table('productos')->where('codigo', 'SH001')->update(['precio_venta' => 15000]);  // Shot Tequila
        DB::table('productos')->where('codigo', 'SH002')->update(['precio_venta' => 12000]);  // Shot Vodka
        DB::table('productos')->where('codigo', 'SH003')->update(['precio_venta' => 10000]);  // Shot Cachaça
        DB::table('productos')->where('codigo', 'SH004')->update(['precio_venta' => 18000]);  // Jägermeister
    }
}
