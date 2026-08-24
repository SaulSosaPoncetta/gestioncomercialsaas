<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE movimientos_inventario MODIFY tipo_movimiento ENUM('ENTRADA_COMPRA','SALIDA_VENTA','AJUSTE_POSITIVO','AJUSTE_NEGATIVO','TRANSFERENCIA','DEVOLUCION_VENTA','DEVOLUCION_COMPRA') NOT NULL");

        Schema::create('notas_credito_venta', function (Blueprint $table) {
            $table->increments('id_nota_credito_venta');
            $table->integer('id_venta');
            $table->string('numero_nota', 30)->unique();
            $table->date('fecha');
            $table->string('motivo', 255);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('monto_impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['EMITIDA', 'ANULADA'])->default('EMITIDA');
            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('restrict');
        });

        Schema::create('detalle_notas_credito_venta', function (Blueprint $table) {
            $table->increments('id_detalle_ncv');
            $table->unsignedInteger('id_nota_credito_venta');
            $table->integer('id_producto');
            $table->decimal('cantidad', 12, 3);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('monto_impuesto', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);

            $table->foreign('id_nota_credito_venta')->references('id_nota_credito_venta')->on('notas_credito_venta')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('restrict');
        });

        Schema::create('notas_credito_compra', function (Blueprint $table) {
            $table->increments('id_nota_credito_compra');
            $table->integer('id_compra');
            $table->string('numero_nota', 30)->unique();
            $table->date('fecha');
            $table->string('motivo', 255);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['EMITIDA', 'ANULADA'])->default('EMITIDA');
            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_compra')->references('id_compra')->on('compras')->onDelete('restrict');
        });

        Schema::create('detalle_notas_credito_compra', function (Blueprint $table) {
            $table->increments('id_detalle_ncc');
            $table->unsignedInteger('id_nota_credito_compra');
            $table->integer('id_producto');
            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);

            $table->foreign('id_nota_credito_compra')->references('id_nota_credito_compra')->on('notas_credito_compra')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_notas_credito_compra');
        Schema::dropIfExists('notas_credito_compra');
        Schema::dropIfExists('detalle_notas_credito_venta');
        Schema::dropIfExists('notas_credito_venta');

        DB::statement("ALTER TABLE movimientos_inventario MODIFY tipo_movimiento ENUM('ENTRADA_COMPRA','SALIDA_VENTA','AJUSTE_POSITIVO','AJUSTE_NEGATIVO','TRANSFERENCIA') NOT NULL");
    }
};