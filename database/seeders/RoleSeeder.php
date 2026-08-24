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

        // Migrar cualquier usuario que haya quedado con el rol viejo 'usuario'
        $rolViejo = Role::where('name', 'usuario')->first();
        if ($rolViejo) {
            foreach ($rolViejo->users as $user) {
                $user->assignRole('vendedor');
                $user->removeRole('usuario');
            }
            $rolViejo->delete();
        }
    }
}