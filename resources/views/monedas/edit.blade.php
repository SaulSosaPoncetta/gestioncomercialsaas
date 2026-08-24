@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-currency-exchange me-2"></i>Editar Moneda</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('monedas.update', $moneda) }}" method="POST">
            @method('PUT')
            @include('monedas._form')
        </form>
    </div>
</div>
@endsection