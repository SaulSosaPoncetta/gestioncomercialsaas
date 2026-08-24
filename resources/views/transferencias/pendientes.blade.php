@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-inbox-fill me-2"></i>Transferencias Pendientes de Recibir</h3>
    <a href="{{ route('transferencias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('transferencias.pendientes') }}" class="d-flex gap-2">
            <select name="id_almacen" class="form-select" style="max-width: 320px;">
                <option value="">Todos los almacenes de destino</option>
                @foreach($almacenes as $alm)
                    <option value="{{ $alm->id_almacen }}" {{ request('id_almacen') == $alm->id_almacen ? 'selected' : '' }}>
                        {{ $alm->nombre }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
            @if(request('id_almacen'))
            <a href="{{ route('transferencias.pendientes') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
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
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendientes as $transferencia)
                <tr>
                    <td>{{ $transferencia->numero_remito }}</td>
                    <td>{{ $transferencia->fecha_envio->format('d/m/Y H:i') }}</td>
                    <td><span class="badge bg-secondary">{{ $transferencia->almacenOrigen->nombre ?? '—' }}</span></td>
                    <td><span class="badge bg-primary">{{ $transferencia->almacenDestino->nombre ?? '—' }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('transferencias.recibir', $transferencia) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-box-arrow-in-down"></i> Recibir
                        </a>
                        <a href="{{ route('transferencias.show', $transferencia) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay transferencias pendientes de recepción.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $pendientes->appends(request()->query())->links() }}
</div>
@endsection