@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-buildings me-2"></i>Panel Superadmin — Empresas</h3>
</div>

<div class="alert alert-dark">
    <i class="bi bi-shield-lock me-1"></i>
    Estás viendo todas las empresas registradas en el sistema, fuera del filtrado normal por empresa.
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('superadmin.empresas.index') }}" class="d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por razón social o CUIT..."
                   value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
            @if(request('buscar'))
            <a href="{{ route('superadmin.empresas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Razón Social</th>
                    <th>CUIT</th>
                    <th>Alta</th>
                    <th class="text-end">Sucursales</th>
                    <th class="text-end">Usuarios</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->razon_social }}</td>
                    <td>{{ $empresa->identificacion_fiscal }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($empresa->creado_en)->format('d/m/Y') }}</td>
                    <td class="text-end">{{ $empresa->sucursales_count }}</td>
                    <td class="text-end">{{ $usuariosPorEmpresa[$empresa->id_empresa] ?? 0 }}</td>
                    <td>
                        @if($empresa->estado)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-danger">Suspendida</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('superadmin.empresas.show', $empresa) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($empresa->estado)
                        <form action="{{ route('superadmin.empresas.suspender', $empresa) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Suspender esta empresa? Sus usuarios no podrán loguearse.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-pause-circle"></i> Suspender
                            </button>
                        </form>
                        @else
                        <form action="{{ route('superadmin.empresas.activar', $empresa) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-play-circle"></i> Reactivar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay empresas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $empresas->appends(request()->query())->links() }}
</div>
@endsection