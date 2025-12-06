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

    public function create(\App\Services\BackupService $backupService)
    {
        $result = $backupService->createBackup();

        if ($result['success']) {
            return redirect()->route('backup.index')->with('success', $result['message']);
        } else {
            return redirect()->route('backup.index')->with('error', 'Fallo al crear backup: ' . $result['message']);
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
