<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configuración de la integración con el sistema contable, por empresa
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('contabilidad_activa')->default(false)->after('fecha_verificacion_pago');
            $table->string('contabilidad_url', 255)->nullable()->after('contabilidad_activa');
            $table->text('contabilidad_token')->nullable()->after('contabilidad_url');        // se guarda encriptado
            $table->boolean('contabilidad_compras')->default(false)->after('contabilidad_token');
            $table->date('contabilidad_desde')->nullable()->after('contabilidad_compras');    // no envía documentos anteriores
        });

        // Estado del envío de cada documento
        Schema::create('sincronizaciones_contables', function (Blueprint $table) {
            $table->id();
            $table->integer('id_empresa');
            $table->string('tipo_origen', 30);       // factura | nota_credito_venta | compra | nota_credito_compra
            $table->string('id_externo', 50);
            $table->enum('estado', ['pendiente', 'ok', 'error', 'omitido'])->default('pendiente');
            $table->text('mensaje')->nullable();
            $table->unsignedInteger('intentos')->default(0);
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();

            $table->unique(['id_empresa', 'tipo_origen', 'id_externo'], 'sincronizacion_contable_unica');
            $table->index(['id_empresa', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sincronizaciones_contables');

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['contabilidad_activa', 'contabilidad_url', 'contabilidad_token', 'contabilidad_compras', 'contabilidad_desde']);
        });
    }
};
