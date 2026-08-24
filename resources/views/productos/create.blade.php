@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-box-seam me-2"></i>Nuevo Producto</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('productos.store') }}" method="POST">
            @include('productos._form')
        </form>
    </div>
</div>
@endsection