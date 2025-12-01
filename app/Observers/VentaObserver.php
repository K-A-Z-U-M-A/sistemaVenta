<?php

namespace App\Observers;

use App\Models\ActivityLog;

class VentaObserver
{
    public function created($venta)
    {
        ActivityLog::log(
            'create',
            "Registró una nueva venta #{$venta->numero_comprobante} por " . number_format($venta->total, 0, ',', '.') . " Gs",
            'Venta',
            $venta->id,
            [
                'numero_comprobante' => $venta->numero_comprobante,
                'total' => $venta->total,
                'impuesto' => $venta->impuesto,
            ]
        );
    }

    public function updated($venta)
    {
        $changes = [];
        foreach ($venta->getDirty() as $key => $value) {
            $changes[$key] = [
                'antes' => $venta->getOriginal($key),
                'despues' => $value
            ];
        }

        ActivityLog::log(
            'update',
            "Modificó la venta #{$venta->numero_comprobante}",
            'Venta',
            $venta->id,
            ['cambios' => $changes]
        );
    }

    public function deleted($venta)
    {
        ActivityLog::log(
            'delete',
            "Anuló la venta #{$venta->numero_comprobante}",
            'Venta',
            $venta->id,
            [
                'numero_comprobante' => $venta->numero_comprobante,
                'total' => $venta->total,
            ]
        );
    }
}
