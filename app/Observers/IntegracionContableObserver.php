<?php

namespace App\Observers;

use App\Jobs\SincronizarComprobanteContable;
use App\Models\Compra;
use App\Models\Factura;
use App\Models\NotaCreditoCompra;
use App\Models\NotaCreditoVenta;
use App\Services\ContabilidadClient;
use Illuminate\Database\Eloquent\Model;

/**
 * Cuando se emite una factura, una nota de crédito o se registra una compra,
 * la envía a la contabilidad al terminar la petición.
 * Si el documento pasa a estado ANULADA, avisa la anulación.
 */
class IntegracionContableObserver
{
    public function created(Model $modelo): void
    {
        if ($tipo = $this->tipo($modelo)) {
            SincronizarComprobanteContable::dispatchAfterResponse($tipo, (int) $modelo->getKey());
        }
    }

    public function updated(Model $modelo): void
    {
        $tipo = $this->tipo($modelo);
        if (! $tipo) {
            return;
        }

        $campoEstado = $modelo instanceof Factura ? 'estado_fiscal' : 'estado';

        if ($modelo->wasChanged($campoEstado) && str_starts_with(strtoupper((string) $modelo->{$campoEstado}), 'ANULAD')) {
            $idEmpresa = $modelo instanceof Factura ? $modelo->venta()->withoutGlobalScopes()->value('id_empresa') : $modelo->id_empresa;
            if ($idEmpresa) {
                app(ContabilidadClient::class)->anular($tipo, (int) $modelo->getKey(), (int) $idEmpresa, 'Anulado en GestiónComercial');
            }
        }
    }

    private function tipo(Model $modelo): ?string
    {
        return match (true) {
            $modelo instanceof Factura           => 'factura',
            $modelo instanceof NotaCreditoVenta  => 'nota_credito_venta',
            $modelo instanceof Compra            => 'compra',
            $modelo instanceof NotaCreditoCompra => 'nota_credito_compra',
            default                              => null,
        };
    }
}
