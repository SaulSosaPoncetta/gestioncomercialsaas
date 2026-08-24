@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transferencias entre Almacenes</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('transferencias.pendientes') }}" class="btn btn-outline-warning">
            <i class="bi bi-inbox-fill me-1"></i> Pendientes de Recibir
        </a>
        <a href="{{ route('transferencias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Transferencia
        </a>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('transferencias.index') }}" class="row g-2">
            <div class="col-md-4">
                <select name="id_almacen" class="form-select">
                    <option value="">Todos los almacenes</option>
                    @foreach($almacenes as $alm)
                        <option value="{{ $alm->id_almacen }}" {{ request('id_almacen') == $alm->id_almacen ? 'selected' : '' }}>
                            {{ $alm->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Enviada</option>
                    <option value="RECIBIDA" {{ request('estado') == 'RECIBIDA' ? 'selected' : '' }}>Recibida</option>
                    <option value="RECIBIDA_CON_DIFERENCIA" {{ request('estado') == 'RECIBIDA_CON_DIFERENCIA' ? 'selected' : '' }}>Recibida con diferencia</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if(request('id_almacen') || request('estado'))
                <a href="{{ route('transferencias.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Remito</th>
                    <th>Fecha Envío</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transferencias as $transferencia)
                <tr>
                    <td>{{ $transferencia->numero_remito }}</td>
                    <td>{{ $transferencia->fecha_envio->format('d/m/Y H:i') }}</td>
                    <td><span class="badge bg-secondary">{{ $transferencia->almacenOrigen->nombre ?? '—' }}</span></td>
                    <td><span class="badge bg-primary">{{ $transferencia->almacenDestino->nombre ?? '—' }}</span></td>
                    <td>
                        @php
                            $color = match($transferencia->estado) {
                                'ENVIADA' => 'warning',
                                'RECIBIDA' => 'success',
                                'RECIBIDA_CON_DIFERENCIA' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ str_replace('_', ' ', $transferencia->estado) }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('transferencias.show', $transferencia) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay transferencias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div