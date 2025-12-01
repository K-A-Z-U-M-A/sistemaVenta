<?php

namespace App\Observers;

use App\Models\ActivityLog;

class CajaObserver
{
    public function created($caja)
    {
        ActivityLog::log(
            'create',
            "Abrió la caja con monto inicial: " . number_format($caja->monto_inicial, 0, ',', '.') . " Gs",
            'Caja',
            $caja->id,
            ['monto_inicial' => $caja->monto_inicial]
        );
    }

    public function updated($caja)
    {
        if ($caja->isDirty('fecha_cierre')) {
            ActivityLog::log(
                'update',
                "Cerró la caja con monto final: " . number_format($caja->monto_final, 0, ',', '.') . " Gs",
                'Caja',
                $caja->id,
                [
                    'monto_inicial' => $caja->monto_inicial,
                    'monto_final' => $caja->monto_final,
                    'diferencia' => $caja->diferencia,
                ]
            );
        }
    }
}
