<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->increments('id_transferencia');
            $table->integer('id_almacen_origen');
            $table->integer('id_almacen_destino');
            $table->string('numero_remito', 30)->unique();
            $table->string('motivo', 255);
            $table->enum('estado', ['ENVIADA', 'RECIBIDA', 'RECIBIDA_CON_DIFERENCIA'])->default('ENVIADA');
            $table->dateTime('fecha_envio');
            $table->dateTime('fecha_recepcion')->nullable();
            $table->string('observaciones_recepcion', 255)->nullable();
            $table->unsignedBigInteger('id_usuario_envio');
            $table->unsignedBigInteger('id_usuario_recepcion')->nullable();

            $table->foreign('id_almacen_origen')->references('id_almacen')->on('almacenes')->onDelete('restrict');
            $table->foreign('id_almacen_destino')->references('id_almacen')->on('almacenes')->onDelete('restrict');
        });

        Schema::create('detalle_transferencias', function (Blueprint $table) {
            $table->increments('id_detalle_transferencia');
            $table->unsignedInteger('id_transferencia');
            $table->integer('id_producto');
            $table->decimal('cantidad_enviada', 12, 3);
            $table->decimal('cantidad_recibida', 12, 3)->nullable();

            $table->foreign('id_transferencia')->references('id_transferencia')->on('transferencias')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_transferencias');
        Schema::dropIfExists('transferencias');
    }
};