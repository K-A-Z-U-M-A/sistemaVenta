<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public function createBackup()
    {
        try {
            // 1. Configuración de DB
            $dbName = config('database.connections.pgsql.database');
            $dbUser = config('database.connections.pgsql.username');
            $dbHost = config('database.connections.pgsql.host');
            $dbPort = config('database.connections.pgsql.port');
            $dbPassword = config('database.connections.pgsql.password');

            // 2. Definir nombre y ruta
            $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
            $storagePath = storage_path("app/backups/");
            
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filePath = $storagePath . $filename;

            // 3. Encontrar pg_dump
            $pgDumpPath = $this->findPgDump();
            
            if (!$pgDumpPath) {
                throw new \Exception("No se encontró pg_dump.exe en las rutas comunes. Instala PostgreSQL o agrega la ruta al PATH.");
            }

            // 4. Construir comando (Windows)
            // Usamos set "VAR=VAL" para manejar mejor los caracteres especiales y espacios
            $command = "set \"PGPASSWORD={$dbPassword}\" && \"{$pgDumpPath}\" -h {$dbHost} -p {$dbPort} -U {$dbUser} -b -v -f \"{$filePath}\" {$dbName} 2>&1";

            // 5. Ejecutar
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                // Limpiar salida de caracteres nulos que a veces aparecen en stderr de Windows
                $errorMsg = implode("\n", array_filter($output));
                
                // Si el mensaje está vacío, usar código de error
                if (empty($errorMsg)) {
                    $errorMsg = "Error desconocido (Código $returnVar). Verifica credenciales y permisos.";
                }

                throw new \Exception("Falló pg_dump: " . substr($errorMsg, 0, 500));
            }

            // 6. Verificar que el archivo se creó y tiene contenido
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                throw new \Exception("El archivo de backup se creó vacío o no existe.");
            }

            return [
                'success' => true,
                'path' => $filePath,
                'filename' => $filename,
                'message' => 'Backup creado exitosamente'
            ];

        } catch (\Exception $e) {
            Log::error("Backup fallido: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function findPgDump()
    {
        // Posibles rutas en Windows
        $paths = [
            'C:\Program Files\PostgreSQL\17\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\16\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\15\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\14\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\13\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\12\bin\pg_dump.exe',
            'C:\PostgreSQL\bin\pg_dump.exe', // Algunas instalaciones custom
        ];

        // Primero verificar si está en el PATH global
        // `where` devuelve 0 si encuentra algo
        exec('where pg_dump', $output, $returnVar);
        if ($returnVar === 0) {
            return 'pg_dump';
        }

        // Buscar en rutas conocidas
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
