<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Services\InicializarEmpresa;
use Illuminate\Database\Seeder;

/**
 * Multi-empresa: cada empresa tiene sus propios tipos de comprobante
 * (y los demás datos mínimos). Se delega en InicializarEmpresa, que solo crea lo que falta.
 */
class TipoComprobanteSeeder extends Seeder
{
    public function run(InicializarEmpresa $inicializar): void
    {
        Empresa::all()->each(fn (Empresa $empresa) => $inicializar->ejecutar($empresa));
    }
}
