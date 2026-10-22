<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Impuesto;
use App\Models\Moneda;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use Illuminate\Database\Eloquent\Model;

/**
 * Datos mínimos para que una empresa nueva pueda operar y facturar:
 * casa central, depósito, impuestos (IVA), moneda, categoría y tipos de comprobante.
 * Es idempotente: solo crea lo que falta (se puede correr sobre empresas existentes).
 */
class InicializarEmpresa
{
    /** @return array<string,int> cantidad creada por tipo de dato */
    public function ejecutar(Empresa $empresa): array
    {
        $id = $empresa->id_empresa;
        $creados = [];

        // Sucursal y depósito
        $sucursal = $this->obtener(Sucursal::class, $id, ['es_casa_matriz' => true])
            ?? $this->obtener(Sucursal::class, $id, []);
        if (! $sucursal) {
            $sucursal = $this->crear(Sucursal::class, $creados, 'sucursales', [
                'id_empresa' => $id, 'nombre' => 'Casa central', 'codigo' => 'CC',
                'es_casa_matriz' => true, 'estado' => true,
            ]);
        }

        if (! $this->obtener(Almacen::class, $id, [])) {
            $this->crear(Almacen::class, $creados, 'almacenes', [
                'id_empresa' => $id, 'id_sucursal' => $sucursal->id_sucursal, 'nombre' => 'Depósito principal', 'estado' => true,
            ]);
        }

        // Impuestos (IVA)
        foreach ([
            ['IVA 21%', 21.00, true],
            ['IVA 10,5%', 10.50, false],
            ['IVA 27%', 27.00, false],
            ['Exento / No gravado', 0.00, false],
        ] as [$nombre, $porcentaje, $predeterminado]) {
            if (! $this->obtener(Impuesto::class, $id, ['porcentaje' => $porcentaje])) {
                $this->crear(Impuesto::class, $creados, 'impuestos', [
                    'id_empresa' => $id, 'nombre' => $nombre, 'porcentaje' => $porcentaje,
                    'es_predeterminado' => $predeterminado, 'estado' => true,
                ]);
            }
        }

        // Moneda base
        if (! $this->obtener(Moneda::class, $id, ['codigo' => 'ARS'])) {
            $this->crear(Moneda::class, $creados, 'monedas', [
                'id_empresa' => $id, 'codigo' => 'ARS', 'nombre' => 'Peso argentino', 'simbolo' => '$',
                'tipo_cambio' => 1, 'es_moneda_base' => ! $this->obtener(Moneda::class, $id, ['es_moneda_base' => true]),
            ]);
        }

        // Categoría por defecto
        if (! $this->obtener(Categoria::class, $id, [])) {
            $this->crear(Categoria::class, $creados, 'categorias', ['id_empresa' => $id, 'nombre' => 'General', 'estado' => true]);
        }

        // Tipos de comprobante (los códigos los entiende la integración contable)
        foreach ([
            ['FACT_A', 'Factura A', 'A0001'],
            ['FACT_B', 'Factura B', 'B0001'],
            ['FACT_C', 'Factura C', 'C0001'],
            ['TICKET', 'Ticket', 'T0001'],
        ] as [$codigo, $descripcion, $serie]) {
            if (! $this->obtener(TipoComprobante::class, $id, ['codigo' => $codigo])) {
                $this->crear(TipoComprobante::class, $creados, 'tipos_comprobante', [
                    'id_empresa' => $id, 'codigo' => $codigo, 'descripcion' => $descripcion,
                    'serie_prefijo' => $serie, 'correlativo_actual' => 0,
                ]);
            }
        }

        return $creados;
    }

    /** Busca sin el filtro por usuario logueado (al registrarse todavía no hay sesión). */
    private function obtener(string $clase, int $idEmpresa, array $condiciones): ?Model
    {
        return $clase::withoutGlobalScopes()->where('id_empresa', $idEmpresa)->where($condiciones)->first();
    }

    /** forceFill: id_empresa no está en el $fillable de todos los modelos */
    private function crear(string $clase, array &$creados, string $etiqueta, array $datos): Model
    {
        $modelo = (new $clase())->forceFill($datos);
        $modelo->save();
        $creados[$etiqueta] = ($creados[$etiqueta] ?? 0) + 1;

        return $modelo;
    }
}
