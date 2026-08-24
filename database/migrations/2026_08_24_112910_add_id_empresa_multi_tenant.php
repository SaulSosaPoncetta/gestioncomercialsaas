<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tablas = [
        'categorias', 'marcas', 'productos', 'personas', 'impuestos', 'monedas',
        'listas_precios', 'almacenes', 'cajas_chicas', 'tipos_comprobante',
        'ventas', 'compras', 'transferencias', 'notas_credito_venta', 'notas_credito_compra',
        'stock', 'movimientos_inventario', 'cuentas_por_cobrar', 'cuentas_por_pagar',
    ];

    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('estado')->default(1)->after('email');
        });

        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                $table->integer('id_empresa')->nullable()->after($this->primaryKeyDe($tabla));
                $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('restrict');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->integer('id_empresa')->nullable()->after('id');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('restrict');
        });

        // Backfill: si ya existe al menos una empresa cargada, le asignamos todos los
        // datos existentes (los que vos ya cargaste) para no perder nada.
        $idEmpresaExistente = DB::table('empresas')->orderBy('id_empresa')->value('id_empresa');

        if ($idEmpresaExistente) {
            foreach ($this->tablas as $tabla) {
                DB::table($tabla)->update(['id_empresa' => $idEmpresaExistente]);
            }
            DB::table('users')->update(['id_empresa' => $idEmpresaExistente]);
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropForeign(['id_empresa']);
                $table->dropColumn('id_empresa');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->dropColumn('id_empresa');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    protected function primaryKeyDe(string $tabla): string
    {
        return match ($tabla) {
            'categorias' => 'id_categoria',
            'marcas' => 'id_marca',
            'productos' => 'id_producto',
            'personas' => 'id_persona',
            'impuestos' => 'id_impuesto',
            'monedas' => 'id_moneda',
            'listas_precios' => 'id_lista_precio',
            'almacenes' => 'id_almacen',
            'cajas_chicas' => 'id_caja',
            'tipos_comprobante' => 'id_tipo_comprobante',
            'ventas' => 'id_venta',
            'compras' => 'id_compra',
            'transferencias' => 'id_transferencia',
            'notas_credito_venta' => 'id_nota_credito_venta',
            'notas_credito_compra' => 'id_nota_credito_compra',
            'stock' => 'id_stock',
            'movimientos_inventario' => 'id_movimiento',
            'cuentas_por_cobrar' => 'id_cxc',
            'cuentas_por_pagar' => 'id_cxp',
        };
    }
};