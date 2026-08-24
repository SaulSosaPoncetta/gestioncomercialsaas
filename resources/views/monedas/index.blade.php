@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-currency-exchange me-2"></i>Monedas</h3>
    <a href="{{ route('monedas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Moneda
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Símbolo</th>
                    <th>Tipo de Cambio</th>
                    <th>Moneda Base</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monedas as $moneda)
                <tr>
                    <td>{{ $moneda->codigo }}</td>
                    <td>{{ $moneda->nombre }}</td>
                    <td>{{ $moneda->simbolo }}</td>
                    <td>{{ number_format($moneda->tipo_cambio, 4) }}</td>
                    <td>
                        @if($moneda->es_moneda_base)
                            <span class="badge bg-primary">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('monedas.edit', $moneda) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('monedas.destroy', $moneda) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta moneda?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay monedas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $monedas->links() }}
</div>
@endsection
