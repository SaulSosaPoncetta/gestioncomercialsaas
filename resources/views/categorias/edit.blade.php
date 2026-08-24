@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-diagram-3 me-2"></i>Editar Categoría</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @method('PUT')
            @include('categorias._form')
        </form>
    </div>
</div>
@endsection