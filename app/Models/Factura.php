<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $primaryKey = 'id_factura';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_tipo_comprobante',
        'numero_factura',
        'fecha_emision',
        'cae_fiscal',
        'vencimiento_cae',
        'subtotal',
        'monto_iva',
        'total_facturado',
        'estado_fiscal',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'vencimiento_cae' => 'date',
        'subtotal' => 'decimal:2',
        'monto_iva' => 'decimal:2',
        'total_facturado' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function tipoComprobante()
    {
        return $this->belongsTo(TipoComprobante::class, 'id_tipo_comprobante', 'id_tipo_comprobante');
    }
}