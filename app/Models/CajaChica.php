<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    use BelongsToEmpresa;
    
    protected $table = 'cajas_chicas';
    protected $primaryKey = 'id_caja';
    public $timestamps = false;

    protected $fillable = [
        'id_sucursal',
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function sesionAbierta()
    {
        return $this->hasOne(AperturaCierreCaja::class, 'id_caja', 'id_caja')->where('estado', 'ABIERTA');
    }
}