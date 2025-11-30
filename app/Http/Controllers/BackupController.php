<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-backup|crear-backup|eliminar-backup', ['only' => ['index']]);
        $this->middleware('permission:crear-backup', ['only' => ['create']]);
        $this->middleware('permission:eliminar-backup', ['only' => ['destroy']]);
    }

    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        $backups = [];

        foreach ($files as $k => $f) {
            if (substr($f, -4) == '.sql' && $disk->exists($f)) {
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => str_replace('backups/', '', $f),
                    'file_size' => $this->formatSizeUnits($disk->size($f)),
                    'last_modified' => Carbon::createFromTimestamp($disk->lastModified($f))->format('d-m-Y H:i:s'),
                ];
            }
        }

        // Ordenar por fecha descendente
        $backups = array_reverse($backups);

        return view('backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            // Configuración de la base de datos
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbHost = env('DB_HOST');
            $dbPort = env('DB_PORT');
            $dbPassword = env('DB_PASSWORD');

            $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
            $storagePath = storage_path("app/backups/");
            
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            $filePath = $storagePath . $filename;

            // Asegurar que el puerto tenga valor
            $dbPort = !empty($dbPort) ? $dbPort : '5432';

            // Intentar encontrar pg_dump en rutas comunes de Windows
            $pgDumpPath = 'pg_dump'; // Por defecto asume que está en el PATH
            $commonPaths = [
                'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\14\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\13\\bin\\pg_dump.exe',
                'C:\\Program Files\\PostgreSQL\\12\\bin\\pg_dump.exe',
            ];

            foreach ($commonPaths as $path) {
                if (file_exists($path)) {
                    $pgDumpPath = "\"$path\""; // Agregar comillas por si hay espacios
                    break;
                }
            }

            // Comando para Windows corregido
            // Usamos 2>&1 para capturar stderr en $output
            // Reordenamos argumentos para mayor seguridad
            $command = "set PGPASSWORD={$dbPassword} && {$pgDumpPath} -h {$dbHost} -p {$dbPort} -U {$dbUser} -F p -b -v -f \"{$filePath}\" {$dbName} 2>&1";

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return redirect()->route('backup.index')->with('success', 'Backup creado exitosamente.');
            } else {
                // Convertir el array de salida a string para ver el error
                $errorOutput = implode("\n", $output);
                // Limitar la longitud del mensaje de error
                $shortError = substr($errorOutput, 0, 500);
                return redirect()->route('backup.index')->with('error', 'Error al crear el backup. Código: ' . $returnVar . '. Detalle: ' . $shortError);
            }

        } catch (\Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    public function download($file_name)
    {
        $file = "backups/" . $file_name;
        $disk = Storage::disk('local');

        if ($disk->exists($file)) {
            return Storage::download($file);
        } else {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }
    }

    public function destroy($file_name)
    {
        $disk = Storage::disk('local');
        $file = "backups/" . $file_name;

        if ($disk->exists($file)) {
            $disk->delete($file);
            return redirect()->back()->with('success', 'Backup eliminado correctamente.');
        } else {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
