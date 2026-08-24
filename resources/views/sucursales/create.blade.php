@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-geo-alt me-2"></i>Nueva Sucursal</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('sucursales.store') }}" method="POST">
            @include('sucursales._form')
        </form>
    </div>
</div>
@endsection