@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-percent me-2"></i>Nuevo Impuesto</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('impuestos.store') }}" method="POST">
            @include('impuestos._form')
        </form>
    </div>
</div>
@endsection