<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            // Tipo de producto: comida, bebida, trago
            $table->enum('tipo_producto', ['comida', 'bebida', 'trago'])->default('comida')->after('nombre');
            // Indica si el trago tiene descuento de 5000 Gs
            $table->boolean('aplica_descuento_trago')->default(false)->after('tipo_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['tipo_producto', 'aplica_descuento_trago']);
        });
    }
};
