@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-tags me-2"></i>Editar Marca</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('marcas.update', $marca) }}" method="POST">
            @method('PUT')
            @include('marcas._form')
        </form>
    </div>
</div>
@endsection