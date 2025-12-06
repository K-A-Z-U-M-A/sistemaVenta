<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:reset-operations {--force : Forzar la ejecución sin preguntar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todas las ventas, compras y movimientos de caja, manteniendo productos y configuraciones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿ESTÁS SEGURO? Esto eliminará PERMANENTEMENTE todo el historial de ventas, compras y caja. Los productos se mantendrán.', false)) {
            $this->info('Operación cancelada.');
            return;
        }

        $this->info('Iniciando limpieza de operaciones...');

        try {
            DB::beginTransaction();

            // Desactivar restricciones de clave foránea temporalmente (PostgreSQL)
            DB::statement('SET CONSTRAINTS ALL DEFERRED');

            // 1. Limpiar detalles de ventas y compras primero (tablas hijas)
            // IMPORTANTE: Los nombres correctos según migraciones son producto_venta y compra_producto
            if (Schema::hasTable('producto_venta')) {
                $this->cleanTable('producto_venta');
            } elseif (Schema::hasTable('detalle_ventas')) {
                $this->cleanTable('detalle_ventas');
            }

            if (Schema::hasTable('compra_producto')) {
                $this->cleanTable('compra_producto');
            } elseif (Schema::hasTable('detalle_compras')) {
                $this->cleanTable('detalle_compras');
            }
            
            // 2. Limpiar tablas principales
            $this->cleanTable('ventas');
            $this->cleanTable('compras');
            $this->cleanTable('cajas');
            
            // 3. Tablas adicionales si existen
            if (Schema::hasTable('devoluciones')) {
                $this->cleanTable('devoluciones');
            }
            
            // 4. Limpiar logs de actividad
            // IMPORTANTE: El nombre correcto es activity_logs (plural)
            if (Schema::hasTable('activity_logs')) {
                $this->cleanTable('activity_logs');
            } elseif (Schema::hasTable('activity_log')) {
                $this->cleanTable('activity_log');
            }

            // 5. Reiniciar secuencias (IDs a 1)
            $tables = ['ventas', 'compras', 'cajas', 'producto_venta', 'compra_producto'];
            if (Schema::hasTable('devoluciones')) $tables[] = 'devoluciones';
            if (Schema::hasTable('activity_logs')) $tables[] = 'activity_logs';

            foreach ($tables as $table) {
                // Obtenemos el nombre de la secuencia (asumiendo convención estándar de Laravel/Postgres)
                $seqName = "{$table}_id_seq";
                // Verificamos si la secuencia existe intentando resetearla
                try {
                    DB::statement("ALTER SEQUENCE IF EXISTS {$seqName} RESTART WITH 1");
                } catch (\Exception $e) {
                     // Si falla, es posible que la secuencia tenga otro nombre, no es crítico
                     $this->warn("No se pudo reiniciar secuencia para $table (quizás no usa ID autoincremental estándar)");
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('--------------------------------------------------');
            $this->info('✅  LIMPIEZA COMPLETADA EXITOSAMENTE');
            $this->info('--------------------------------------------------');
            $this->info('Se han eliminado: Ventas, Compras, Cajas e Historial.');
            $this->info('Se han CONSERVADO: Productos, Categorías, Usuarios, Clientes y Proveedores.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Ocurrió un error: ' . $e->getMessage());
        }
    }

    private function cleanTable($tableName)
    {
        $count = DB::table($tableName)->count();
        DB::table($tableName)->delete();
        $this->line("Limpiada tabla: $tableName ($count registros eliminados)");
    }
}
