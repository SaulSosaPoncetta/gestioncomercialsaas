@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-percent me-2"></i>Editar Impuesto</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('impuestos.update', $impuesto) }}" method="POST">
            @method('PUT')
            @include('impuestos._form')
        </form>
    </div>
</div>
@endsection