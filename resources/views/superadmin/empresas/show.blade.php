@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-building me-2"></i>{{ $empresa->razon_social }}</h3>
    <a href="{{ route('superadmin.empresas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">CUIT</div>
                <div class="fw-semibold">{{ $empresa->identificacion_fiscal }}</div>
                <div class="text-muted small mt-2">Fecha de Alta</div>
                <div>{{ \Illuminate\Support\Carbon::parse($empresa->creado_en)->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Estado</div>
                @if($empresa->estado)
                    <span class="badge bg-success fs-6">Activa</span>
                @else
                    <span class="badge bg-danger fs-6">Suspendida</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                @if($empresa->estado)
                <form action="{{ route('superadmin.empresas.suspender', $empresa) }}" method="POST"
                      onsubmit="return confirm('¿Suspender esta empresa?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-pause-circle me-1"></i> Suspender Empresa
                    </button>
                </form>
                @else
                <form action="{{ route('superadmin.empresas.activar', $empresa) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-success w-100">
                        <i class="bi bi-play-circle me-1"></i> Reactivar Empresa
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-people me-1"></i> Usuarios de esta Empresa
    </div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Alta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @foreach($usuario->getRoleNames() as $rol)
                            <span class="badge bg-secondary">{{ $rol }}</span>
                        @endforeach
                    </td>
                    <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No hay usuarios en esta empresa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection