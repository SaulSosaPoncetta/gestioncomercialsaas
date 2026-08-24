@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cash-stack me-2"></i>Editar Caja</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('cajas.update', $caja) }}" method="POST">
            @method('PUT')
            @include('cajas._form')
        </form>
    </div>
</div>
@endsection