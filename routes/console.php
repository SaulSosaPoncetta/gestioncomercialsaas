<?php

use App\Models\Compra;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\NotaCreditoCompra;
use App\Models\NotaCreditoVenta;
use App\Models\SincronizacionContable;
use App\Services\ContabilidadClient;
use App\Services\HubClientes;
use App\Services\InicializarEmpresa;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Datos iniciales por empresa (multi-empresa)
|--------------------------------------------------------------------------
| php artisan empresas:inicializar             → completa lo que le falte a cada empresa
| php artisan empresas:inicializar --empresa=3 → solo una empresa
*/
Artisan::command('empresas:inicializar {--empresa=}', function (InicializarEmpresa $inicializar) {
    $empresas = Empresa::when($this->option('empresa'), fn ($q, $id) => $q->where('id_empresa', $id))->get();

    foreach ($empresas as $empresa) {
        $creados = $inicializar->ejecutar($empresa);
        $detalle = $creados
            ? collect($creados)->map(fn ($n, $k) => "{$n} {$k}")->implode(', ')
            : 'ya tenía todo';
        $this->line("✓ {$empresa->razon_social}: {$detalle}");
    }

    $this->info("Listo: {$empresas->count()} empresa(s) revisada(s).");
})->purpose('Crea sucursal, depósito, impuestos, moneda y tipos de comprobante a las empresas que no los tengan');

/*
|--------------------------------------------------------------------------
| MiGestión Panel (hub)
|--------------------------------------------------------------------------
| php artisan hub:registrar-empresas             → da de alta en el hub todas las empresas (las ya registradas se actualizan)
| php artisan hub:registrar-empresas --empresa=3 → solo una
*/
Artisan::command('hub:registrar-empresas {--empresa=}', function (HubClientes $hub) {
    if (! config('migestion_hub.url') || ! config('migestion_hub.api_key')) {
        $this->error('Falta configurar MIGESTION_HUB_URL y MIGESTION_HUB_API_KEY en el .env');
        return;
    }

    $empresas = Empresa::when($this->option('empresa'), fn ($q, $id) => $q->where('id_empresa', $id))->get();

    foreach ($empresas as $empresa) {
        $this->line(($hub->registrar($empresa) ? '✓ ' : '✗ ') . "{$empresa->razon_social} ({$empresa->identificacion_fiscal})");
    }

    $this->info("Listo: {$empresas->count()} empresa(s).");
})->purpose('Registra las empresas en MiGestión Panel para la vinculación automática');

/*
|--------------------------------------------------------------------------
| Integración contable
|--------------------------------------------------------------------------
| php artisan contabilidad:sincronizar                    → reintenta los envíos con error
| php artisan contabilidad:sincronizar --desde=2026-10-01 → envía todo lo emitido desde esa fecha que no se haya enviado
| php artisan contabilidad:sincronizar --empresa=3        → solo una empresa
*/
Artisan::command('contabilidad:sincronizar {--desde=} {--empresa=}', function (ContabilidadClient $cliente) {
    $empresas = Empresa::where('contabilidad_activa', true)
        ->when($this->option('empresa'), fn ($q, $id) => $q->where('id_empresa', $id))
        ->get()
        ->filter(fn ($e) => $e->integraConContabilidad());

    if ($empresas->isEmpty()) {
        $this->warn('No hay empresas con la integración contable activa.');
        return;
    }

    $enviados = 0;
    $errores = 0;

    foreach ($empresas as $empresa) {
        $this->info("Empresa {$empresa->razon_social}");
        $pendientes = collect();

        // 1) Reintentos de los envíos con error
        SincronizacionContable::where('id_empresa', $empresa->id_empresa)->where('estado', 'error')->get()
            ->each(fn ($s) => $pendientes->push([$s->tipo_origen, (int) $s->id_externo]));

        // 2) Documentos desde una fecha que todavía no se enviaron
        if ($desde = $this->option('desde')) {
            $enviadosOk = SincronizacionContable::where('id_empresa', $empresa->id_empresa)
                ->whereIn('estado', ['ok', 'omitido'])->get()
                ->map(fn ($s) => $s->tipo_origen . '|' . $s->id_externo)->flip();

            $agregar = function (string $tipo, iterable $ids) use ($pendientes, $enviadosOk) {
                foreach ($ids as $id) {
                    if (! isset($enviadosOk["{$tipo}|{$id}"])) {
                        $pendientes->push([$tipo, (int) $id]);
                    }
                }
            };

            $agregar('factura', Factura::whereDate('fecha_emision', '>=', $desde)
                ->whereHas('venta', fn ($q) => $q->withoutGlobalScopes()->where('id_empresa', $empresa->id_empresa))
                ->pluck('id_factura'));
            $agregar('nota_credito_venta', NotaCreditoVenta::withoutGlobalScopes()->where('id_empresa', $empresa->id_empresa)
                ->whereDate('fecha', '>=', $desde)->pluck('id_nota_credito_venta'));

            if ($empresa->contabilidad_compras) {
                $agregar('compra', Compra::withoutGlobalScopes()->where('id_empresa', $empresa->id_empresa)
                    ->whereDate('fecha_emision', '>=', $desde)->pluck('id_compra'));
                $agregar('nota_credito_compra', NotaCreditoCompra::withoutGlobalScopes()->where('id_empresa', $empresa->id_empresa)
                    ->whereDate('fecha', '>=', $desde)->pluck('id_nota_credito_compra'));
            }
        }

        foreach ($pendientes->unique(fn ($p) => $p[0] . '|' . $p[1]) as [$tipo, $id]) {
            $r = $cliente->sincronizar($tipo, $id);
            if ($r?->estado === 'error') {
                $errores++;
                $this->line("  <error>✗</error> {$tipo} #{$id}: {$r->mensaje}");
            } elseif ($r) {
                $enviados++;
                $this->line("  ✓ {$tipo} #{$id}: {$r->mensaje}");
            }
        }
    }

    $this->info("Listo: {$enviados} enviados, {$errores} con error.");
})->purpose('Envía a la contabilidad los documentos pendientes o con error');

// Reintento automático de los envíos con error (requiere el cron de schedule:run en el servidor)
Schedule::command('contabilidad:sincronizar')->hourly()->withoutOverlapping();
