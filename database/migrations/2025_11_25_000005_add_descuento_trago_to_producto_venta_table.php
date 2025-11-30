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
        Schema::table('producto_venta', function (Blueprint $table) {
            // Indica si este item es un trago con descuento aplicado
            $table->boolean('es_trago_con_descuento')->default(false)->after('descuento');
            // Monto del descuento de trago (5000 Gs)
            $table->decimal('descuento_trago', 10, 2)->default(0)->after('es_trago_con_descuento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto_venta', function (Blueprint $table) {
            $table->dropColumn(['es_trago_con_descuento', 'descuento_trago']);
        });
    }
};
