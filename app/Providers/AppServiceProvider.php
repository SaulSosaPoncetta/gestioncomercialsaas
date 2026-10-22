<?php

namespace App\Providers;

use App\Models\Compra;
use App\Models\Factura;
use App\Models\NotaCreditoCompra;
use App\Models\NotaCreditoVenta;
use App\Observers\IntegracionContableObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Integración con el sistema contable de MiGestión
        Factura::observe(IntegracionContableObserver::class);
        NotaCreditoVenta::observe(IntegracionContableObserver::class);
        Compra::observe(IntegracionContableObserver::class);
        NotaCreditoCompra::observe(IntegracionContableObserver::class);

        /*
         * Multi-empresa: igual que "exists" pero el registro tiene que ser de la empresa del usuario.
         * Uso: 'id_cliente' => 'required|de_empresa:personas,id_persona'
         * (el superadmin no tiene filtro).
         */
        Validator::extend('de_empresa', function ($attribute, $value, $parameters) {
            [$tabla, $columna] = [$parameters[0], $parameters[1] ?? 'id'];
            $consulta = DB::table($tabla)->where($columna, $value);

            $usuario = auth()->user();
            if ($usuario && ! $usuario->hasRole('superadmin')) {
                $consulta->where('id_empresa', $usuario->id_empresa);
            }

            return $consulta->exists();
        });
        Validator::replacer('de_empresa', fn ($message, $attribute) => "El valor seleccionado en {$attribute} no es válido.");
    }
}
