@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-building me-2"></i>Editar Almacén</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('almacenes.update', $almacen) }}" method="POST">
            @method('PUT')
            @include('almacenes._form')
        </form>
    </div>
</div>
@endsection