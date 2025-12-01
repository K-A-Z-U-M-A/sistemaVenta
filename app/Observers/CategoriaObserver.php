<?php

namespace App\Observers;

use App\Models\ActivityLog;

class CategoriaObserver
{
    public function created($categoria)
    {
        ActivityLog::log(
            'create',
            "Creó la categoría: {$categoria->caracteristica->nombre}",
            'Categoria',
            $categoria->id,
            ['nombre' => $categoria->caracteristica->nombre]
        );
    }

    public function updated($categoria)
    {
        ActivityLog::log(
            'update',
            "Modificó la categoría: {$categoria->caracteristica->nombre}",
            'Categoria',
            $categoria->id
        );
    }

    public function deleted($categoria)
    {
        ActivityLog::log(
            'delete',
            "Eliminó la categoría: {$categoria->caracteristica->nombre}",
            'Categoria',
            $categoria->id,
            ['nombre' => $categoria->caracteristica->nombre]
        );
    }
}
