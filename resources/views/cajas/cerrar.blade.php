@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-lock me-2"></i>Cerrar Caja — {{ $sesion->caja->nombre }}</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="alert alert-info">
            Según el sistema, el saldo actual de la caja debería ser <strong>${{ number_format($saldoSistema, 2) }}</strong>.
            Contá el efectivo real y cargalo abajo para calcular la diferencia (sobrante o faltante).
        </div>

        <form action="{{ route('cajas.guardarCierre', $sesion) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Monto Final Real (contado en caja) *</label>
                <input type="number" step="0.01" min="0" name="monto_final_real"
                       class="form-control @error('monto_final_real') is-invalid @enderror"
                       value="{{ old('monto_final_real', $saldoSistema) }}" required autofocus>
                @error('monto_final_real') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-danger"><i class="bi bi-lock me-1"></i> Confirmar Cierre</button>
                <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection