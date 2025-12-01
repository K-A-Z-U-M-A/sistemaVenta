<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ProductoObserver
{
    public function created($producto)
    {
        ActivityLog::log(
            'create',
            "Creó el producto: {$producto->nombre} (Código: {$producto->codigo})",
            'Producto',
            $producto->id,
            [
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'precio_venta' => $producto->precio_venta,
            ]
        );
    }

    public function updated($producto)
    {
        $changes = [];
        foreach ($producto->getDirty() as $key => $value) {
            $changes[$key] = [
                'antes' => $producto->getOriginal($key),
                'despues' => $value
            ];
        }

        ActivityLog::log(
            'update',
            "Modificó el producto: {$producto->nombre}",
            'Producto',
            $producto->id,
            ['cambios' => $changes]
        );
    }

    public function deleted($producto)
    {
        ActivityLog::log(
            'delete',
            "Eliminó el producto: {$producto->nombre} (Código: {$producto->codigo})",
            'Producto',
            $producto->id,
            [
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
            ]
        );
    }
}
