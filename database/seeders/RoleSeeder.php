<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'ver-costos',
            'gestionar-configuracion',
            'gestionar-catalogo',
            'gestionar-personas',
            'gestionar-compras',
            'gestionar-ventas',
            'gestionar-cajas',
            'gestionar-cobros-pagos',
            'eliminar-registros',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permisos);

        $vendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $vendedor->syncPermissions(['gestionar-ventas']);

        // El rol superadmin no lleva permisos de Spatie propios: el trait
        // BelongsToEmpresa ya lo detecta por nombre de rol y le desactiva
        // el filtrado por empresa, viendo todo el sistema sin restricción.
        Role::firstOrCreate(['name' => 'superadmin']);
    }
}