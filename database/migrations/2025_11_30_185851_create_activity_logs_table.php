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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete, login, logout, etc
            $table->string('model_type')->nullable(); // Producto, Venta, Usuario, etc
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description'); // Descripción legible de la acción
            $table->json('properties')->nullable(); // Datos antes/después del cambio
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
