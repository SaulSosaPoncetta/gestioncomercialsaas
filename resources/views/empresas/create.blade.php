@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-building-gear me-2"></i>Nueva Empresa</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('empresas.store') }}" method="POST">
            @include('empresas._form')
        </form>
    </div>
</div>
@endsection