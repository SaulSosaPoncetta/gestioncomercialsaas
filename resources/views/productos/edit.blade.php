@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-box-seam me-2"></i>Editar Producto</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('productos.update', $producto) }}" method="POST">
            @method('PUT')
            @include('productos._form')
        </form>
    </div>
</div>
@endsection