<?php

namespace App\Observers;

use App\Models\ActivityLog;

class UserObserver
{
    public function created($user)
    {
        ActivityLog::log(
            'create',
            "Creó el usuario: {$user->name} ({$user->email})",
            'User',
            $user->id,
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }

    public function updated($user)
    {
        $changes = [];
        foreach ($user->getDirty() as $key => $value) {
            if ($key !== 'password') { // No registrar cambios de contraseña
                $changes[$key] = [
                    'antes' => $user->getOriginal($key),
                    'despues' => $value
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::log(
                'update',
                "Modificó el usuario: {$user->name}",
                'User',
                $user->id,
                ['cambios' => $changes]
            );
        }
    }

    public function deleted($user)
    {
        ActivityLog::log(
            'delete',
            "Eliminó el usuario: {$user->name} ({$user->email})",
            'User',
            $user->id,
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }
}
