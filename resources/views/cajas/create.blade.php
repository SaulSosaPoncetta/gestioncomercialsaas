@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cash-stack me-2"></i>Nueva Caja</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('cajas.store') }}" method="POST">
            @include('cajas._form')
        </form>
    </div>
</div>
@endsection