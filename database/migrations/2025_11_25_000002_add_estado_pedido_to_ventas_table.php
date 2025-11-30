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
        Schema::table('ventas', function (Blueprint $table) {
            // Cambiar el campo estado a enum con los nuevos estados
            $table->enum('estado_pedido', ['pendiente', 'completado', 'cancelado'])->default('pendiente')->after('estado');
            // Número de mesa o identificador de pedido
            $table->string('numero_mesa', 20)->nullable()->after('numero_comprobante');
            // Descuento total de tragos aplicado (5000 Gs por trago)
            $table->decimal('descuento_tragos', 10, 2)->default(0)->after('impuesto');
            // Ganancia transferida de tragos a comida
            $table->decimal('ganancia_tragos_a_comida', 10, 2)->default(0)->after('descuento_tragos');
            // Notas del pedido
            $table->text('notas')->nullable()->after('numero_mesa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['estado_pedido', 'numero_mesa', 'descuento_tragos', 'ganancia_tragos_a_comida', 'notas']);
        });
    }
};
