<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Registrar actividad de creación
     */
    protected function logCreated($model, $description = null)
    {
        $modelName = class_basename($model);
        $modelIdentifier = property_exists($model, 'nombre') && $model->nombre 
            ? $model->nombre 
            : (property_exists($model, 'name') && $model->name ? $model->name : $model->id);
        $desc = $description ? $description : "{$modelName} creado: {$modelIdentifier}";
        
        ActivityLog::log(
            'create',
            $desc,
            $modelName,
            $model->id,
            ['attributes' => $model->toArray()]
        );
    }

    /**
     * Registrar actividad de actualización
     */
    protected function logUpdated($model, $description = null)
    {
        $modelName = class_basename($model);
        $modelIdentifier = property_exists($model, 'nombre') && $model->nombre 
            ? $model->nombre 
            : (property_exists($model, 'name') && $model->name ? $model->name : $model->id);
        $desc = $description ? $description : "{$modelName} actualizado: {$modelIdentifier}";
        
        ActivityLog::log(
            'update',
            $desc,
            $modelName,
            $model->id,
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges()
            ]
        );
    }

    /**
     * Registrar actividad de eliminación
     */
    protected function logDeleted($model, $description = null)
    {
        $modelName = class_basename($model);
        $modelIdentifier = property_exists($model, 'nombre') && $model->nombre 
            ? $model->nombre 
            : (property_exists($model, 'name') && $model->name ? $model->name : $model->id);
        $desc = $description ? $description : "{$modelName} eliminado: {$modelIdentifier}";
        
        ActivityLog::log(
            'delete',
            $desc,
            $modelName,
            $model->id,
            ['attributes' => $model->toArray()]
        );
    }

    /**
     * Registrar actividad personalizada
     */
    protected function logCustomActivity($action, $description, $model = null)
    {
        ActivityLog::log(
            $action,
            $description,
            $model ? class_basename($model) : null,
            $model ? $model->id : null
        );
    }
}
