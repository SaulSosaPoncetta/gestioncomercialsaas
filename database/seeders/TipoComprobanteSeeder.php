<?php

namespace Database\Seeders;

use App\Models\TipoComprobante;
use Illuminate\Database\Seeder;

class TipoComprobanteSeeder extends Seeder
{
    public function run(): void
    {
        TipoComprobante::firstOrCreate(
            ['codigo' => 'FACT_B'],
            ['descripcion' => 'Factura B', 'serie_prefijo' => 'B0001', 'correlativo_actual' => 0]
        );

        TipoComprobante::firstOrCreate(
            ['codigo' => 'TICKET'],
            ['descripcion' => 'Ticket', 'serie_prefijo' => 'T0001', 'correlativo_actual' => 0]
        );
    }
}