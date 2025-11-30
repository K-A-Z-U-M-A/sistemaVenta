<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Hacer cliente_id nullable para comida rápida
            $table->foreignId('cliente_id')->nullable()->change();
            // Agregar nombre de orden
            $table->string('nombre_orden', 100)->nullable()->after('numero_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('nombre_orden');
            // No podemos revertir el nullable sin perder datos
        });
    }
};
