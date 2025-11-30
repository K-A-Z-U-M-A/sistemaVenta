<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BackupPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'ver-backup',
            'crear-backup',
            'eliminar-backup',
        ];

        // Crear permisos si no existen
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos al rol de administrador
        $role = Role::where('name', 'administrador')->first();
        
        if ($role) {
            $role->givePermissionTo($permissions);
        }
    }
}
