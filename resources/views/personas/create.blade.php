@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-people me-2"></i>Nueva Persona</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('personas.store') }}" method="POST">
            @include('personas._form')
        </form>
    </div>
</div>
@endsection