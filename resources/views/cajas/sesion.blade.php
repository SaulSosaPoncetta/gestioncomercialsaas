@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Sesión de Caja — {{ $sesion->caja->nombre }}</h3>
    <a href="{{ route('cajas.formularioCierre', $sesion) }}" class="btn btn-danger">
        <i class="bi bi-lock me-1"></i> Cerrar Caja
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Monto Inicial</div>
                <div class="fs-5 fw-bold">${{ number_format($sesion->monto_inicial, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Ingresos</div>
                <div class="fs-5 fw-bold text-success">${{ number_format($ingresos, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Egresos</div>
                <div class="fs-5 fw-bold text-danger">${{ number_format($egresos, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100 border-primary">
            <div class="card-body">
                <div class="text-muted small">Saldo Actual (sistema)</div>
                <div class="fs-5 fw-bold">${{ number_format($saldoSistema, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h6 class="mb-3">Registrar Movimiento</h6>
        <form action="{{ route('cajas.guardarMovimiento', $sesion) }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-2">
                <label class="form-label small">Tipo *</label>
                <select name="tipo" class="form-select" required>
                    <option value="INGRESO">Ingreso</option>
                    <option value="EGRESO">Egreso</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Concepto *</label>
                <input type="text" name="concepto" class="form-control" placeholder="Cobro venta, retiro, pago servicio..." required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Monto *</label>
                <input type="number" step="0.01" min="0.01" name="monto" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Forma de Pago *</label>
                <select name="id_forma_pago" class="form-select" required>
                    <option value="1">Efectivo</option>
                    <option value="2">Tarjeta Débito</option>
                    <option value="3">Tarjeta Crédito</option>
                    <option value="4">Transferencia</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Agregar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sesion->movimientos->sortByDesc('fecha') as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $mov->tipo == 'INGRESO' ? 'success' : 'danger' }}">{{ $mov->tipo }}</span>
                    </td>
                    <td>{{ $mov->concepto }}</td>
                    <td class="text-end">${{ number_format($mov->monto, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No hay movimientos registrados todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection