@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cash-coin me-2"></i>Editar Lista de Precios</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('listas-precios.update', $listaPrecio) }}" method="POST">
            @method('PUT')
            @include('listas-precios._form')
        </form>
    </div>
</div>
@endsection