@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-people me-2"></i>Editar Persona</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('personas.update', $persona) }}" method="POST">
            @method('PUT')
            @include('personas._form')
        </form>
    </div>
</div>
@endsection