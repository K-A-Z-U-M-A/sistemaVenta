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
            $table->enum('forma_pago', ['efectivo', 'tarjeta', 'transferencia'])->default('efectivo')->after('total');
            $table->foreignId('caja_id')->nullable()->after('forma_pago')->constrained('cajas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropColumn(['forma_pago', 'caja_id']);
        });
    }
};
