<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-empresa: los campos que eran únicos en TODO el sistema pasan a ser únicos POR EMPRESA.
 * Así dos clientes del SaaS pueden tener el mismo cliente (DNI/CUIT), el mismo SKU,
 * el mismo tipo de comprobante (FACT_B) o la misma numeración de facturas.
 *
 * Además agrega id_empresa a facturas (antes solo se llegaba por la venta).
 */
return new class extends Migration
{
    /** tabla => columnas que pasan a ser únicas por empresa */
    private array $unicos = [
        'sucursales'           => ['codigo'],
        'monedas'              => ['codigo'],
        'personas'             => ['numero_documento'],
        'marcas'               => ['nombre'],
        'productos'            => ['sku', 'codigo_barras'],
        'tipos_comprobante'    => ['codigo'],
        'notas_credito_venta'  => ['numero_nota'],
        'notas_credito_compra' => ['numero_nota'],
        'facturas'             => ['numero_factura'],
    ];

    public function up(): void
    {
        // 1) facturas.id_empresa (se completa desde la venta)
        if (! Schema::hasColumn('facturas', 'id_empresa')) {
            Schema::table('facturas', function (Blueprint $table) {
                $table->integer('id_empresa')->nullable()->after('id_factura');
            });

            DB::statement('UPDATE facturas f JOIN ventas v ON v.id_venta = f.id_venta SET f.id_empresa = v.id_empresa');

            Schema::table('facturas', function (Blueprint $table) {
                $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('restrict');
                $table->index('id_empresa');
            });
        }

        // 2) Únicos globales → únicos por empresa
        foreach ($this->unicos as $tabla => $columnas) {
            foreach ($columnas as $columna) {
                $this->quitarUnicoGlobal($tabla, $columna);

                Schema::table($tabla, function (Blueprint $table) use ($tabla, $columna) {
                    $table->unique(['id_empresa', $columna], "{$tabla}_empresa_{$columna}_unique");
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->unicos as $tabla => $columnas) {
            foreach ($columnas as $columna) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla, $columna) {
                    $table->dropUnique("{$tabla}_empresa_{$columna}_unique");
                    $table->unique($columna);
                });
            }
        }

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->dropIndex(['id_empresa']);
            $table->dropColumn('id_empresa');
        });
    }

    /**
     * Borra los índices UNIQUE de una sola columna sobre $columna.
     * Se buscan por nombre en information_schema porque los creó el SQL original
     * (no siguen la convención de nombres de Laravel).
     */
    private function quitarUnicoGlobal(string $tabla, string $columna): void
    {
        $indices = DB::select(
            'SELECT INDEX_NAME, COUNT(*) AS cols, SUM(COLUMN_NAME = ?) AS coincide
               FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND NON_UNIQUE = 0 AND INDEX_NAME <> "PRIMARY"
              GROUP BY INDEX_NAME',
            [$columna, $tabla]
        );

        foreach ($indices as $i) {
            if ((int) $i->cols === 1 && (int) $i->coincide === 1) {
                DB::statement("ALTER TABLE `{$tabla}` DROP INDEX `{$i->INDEX_NAME}`");
            }
        }
    }
};
