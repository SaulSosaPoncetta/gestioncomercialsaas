<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $sql = file_get_contents(database_path('sql/gestion_comercial_schema.sql'));

        // Quitamos el CREATE DATABASE / USE, porque Laravel ya se conecta a la base configurada en .env
        $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
        $sql = preg_replace('/USE\s+`?\w+`?\s*;/i', '', $sql);

        DB::unprepared($sql);

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'logs_auditoria', 'cobros_pagos_transacciones', 'cuentas_por_cobrar', 'facturas',
            'tipos_comprobante', 'detalle_ventas', 'ventas', 'cuentas_por_pagar', 'detalle_compras',
            'compras', 'movimientos_caja', 'aperturas_cierres_caja', 'cajas_chicas',
            'movimientos_inventario', 'stock', 'almacenes', 'precios_productos', 'listas_precios',
            'productos', 'marcas', 'categorias', 'personas', 'monedas', 'impuestos',
            'sucursales', 'empresas',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }
};