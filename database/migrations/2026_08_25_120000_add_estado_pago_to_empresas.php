<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Refleja el estado de la suscripción tal como lo administra el
            // panel central (MiGestión Panel). "sin_verificar" es el estado
            // inicial hasta la primera consulta/webhook del hub.
            $table->enum('estado_pago', ['sin_verificar', 'activa', 'vencida', 'suspendida', 'cancelada'])
                ->default('sin_verificar')
                ->after('estado');
            $table->timestamp('fecha_verificacion_pago')->nullable()->after('estado_pago');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['estado_pago', 'fecha_verificacion_pago']);
        });
    }
};