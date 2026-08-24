<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToEmpresa
{
    protected static function bootBelongsToEmpresa(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->hasRole('superadmin') && auth()->user()->id_empresa) {
                $builder->where($builder->getModel()->getTable() . '.id_empresa', auth()->user()->id_empresa);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->id_empresa) && auth()->check() && auth()->user()->id_empresa) {
                $model->id_empresa = auth()->user()->id_empresa;
            }
        });
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'id_empresa', 'id_empresa');
    }
}