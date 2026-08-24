@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-diagram-3 me-2"></i>Nueva Categoría</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('categorias.store') }}" method="POST">
            @include('categorias._form')
        </form>
    </div>
</div>
@endsection