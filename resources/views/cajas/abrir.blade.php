@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-unlock me-2"></i>Abrir Caja — {{ $caja->nombre }}</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('cajas.guardarApertura', $caja) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Monto Inicial (fondo de caja) *</label>
                <input type="number" step="0.01" min="0" name="monto_inicial"
                       class="form-control @error('monto_inicial') is-invalid @enderror"
                       value="{{ old('monto_inicial', '0.00') }}" required autofocus>
                @error('monto_inicial') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success"><i class="bi bi-unlock me-1"></i> Abrir Caja</button>
                <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection