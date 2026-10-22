<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'nombre_fantasia',
        'identificacion_fiscal',
        'direccion',
        'telefono',
        'email',
        'estado',
        'estado_pago',
        'fecha_verificacion_pago',
        'contabilidad_activa',
        'contabilidad_url',
        'contabilidad_token',
        'contabilidad_compras',
        'contabilidad_desde',
    ];

    protected $hidden = [
        'contabilidad_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_verificacion_pago' => 'datetime',
        'contabilidad_activa' => 'boolean',
        'contabilidad_token' => 'encrypted',
        'contabilidad_compras' => 'boolean',
        'contabilidad_desde' => 'date',
    ];

    public function alDiaConElPago(): bool
    {
        return in_array($this->estado_pago, ['activa', 'sin_verificar'], true);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    /** ¿Está configurada y activa la integración con el sistema contable? */
    public function integraConContabilidad(): bool
    {
        return $this->contabilidad_activa && $this->contabilidad_token && $this->urlContabilidad();
    }

    public function urlContabilidad(): ?string
    {
        $url = $this->contabilidad_url ?: config('contabilidad.url');

        return $url ? rtrim($url, '/') : null;
    }
}
