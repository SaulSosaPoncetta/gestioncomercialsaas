@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cash-coin me-2"></i>Nueva Lista de Precios</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('listas-precios.store') }}" method="POST">
            @include('listas-precios._form')
        </form>
    </div>
</div>
@endsection