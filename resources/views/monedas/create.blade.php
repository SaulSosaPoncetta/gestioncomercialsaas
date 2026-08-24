@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-currency-exchange me-2"></i>Nueva Moneda</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('monedas.store') }}" method="POST">
            @include('monedas._form')
        </form>
    </div>
</div>
@endsection