<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';
    protected $primaryKey = 'id_mov_caja';
    const CREATED_AT = 'fecha';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_sesion_caja',
        'tipo',
        'concepto',
        'monto',
        'id_forma_pago',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];


    /**
     * Multi-empresa: esta tabla no tiene id_empresa; se filtra por la sesión de caja,
     * que sí está filtrada por la empresa del usuario logueado.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->hasRole('superadmin')) {
                $builder->whereHas('sesion');
            }
        });
    }
    public function sesion()
    {
        return $this->belongsTo(AperturaCierreCaja::class, 'id_sesion_caja', 'id_sesion_caja');
    }
}