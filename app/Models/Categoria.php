<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria_padre',
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_padre', 'id_categoria');
    }

    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'id_categoria_padre', 'id_categoria');
    }
}