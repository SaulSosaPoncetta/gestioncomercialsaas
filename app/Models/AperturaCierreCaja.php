<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AperturaCierreCaja extends Model
{
    protected $table = 'aperturas_cierres_caja';
    protected $primaryKey = 'id_sesion_caja';
    public $timestamps = false;

    protected $fillable = [
        'id_caja',
        'id_usuario_apertura',
        'id_usuario_cierre',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final_sistema',
        'monto_final_real',
        'diferencia',
        'estado',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final_sistema' => 'decimal:2',
        'monto_final_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(CajaChica::class, 'id_caja', 'id_caja');
    }

    public function usuarioApertura()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario_apertura', 'id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario_cierre', 'id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_sesion_caja', 'id_sesion_caja');
    }
}