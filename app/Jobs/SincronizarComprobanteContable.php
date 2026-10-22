<?php

namespace App\Jobs;

use App\Services\ContabilidadClient;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Envía un documento a la contabilidad. Se despacha con dispatchAfterResponse():
 * corre al terminar la petición (no hace falta un worker de colas) y no demora la pantalla.
 */
class SincronizarComprobanteContable
{
    use Dispatchable;

    public function __construct(public string $tipo, public int $id)
    {
    }

    public function handle(ContabilidadClient $cliente): void
    {
        $cliente->sincronizar($this->tipo, $this->id);
    }
}
