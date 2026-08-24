@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-geo-alt me-2"></i>Editar Sucursal</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('sucursales.update', $sucursal) }}" method="POST">
            @method('PUT')
            @include('sucursales._form')
        </form>
    </div>
</div>
@endsection