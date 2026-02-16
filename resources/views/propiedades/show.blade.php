@extends('layouts.app')

@section('content')

<div class=" container">
    <h1>Detalle del Propiedad</h1>

    <p><strong>Referencia:</strong>{{ $propiedad->referencia_catastral }}</p>
    <p><strong>Dirección:</strong>{{ $propiedad->direccion_text }}</p>
    <p><strong>Uso:</strong>{{ $propiedad->uso }}</p>
    <p><strong>Superficie:</strong>{{ $propiedad->superficie_m2 }}</p>

    <h3>Unidades Constructivas</h3>

    <ul>
        @forelse ($propiedad->unidadesConstructivas ?? [] as $unidad )
            <li>
                {{ $unidad->tipo_unidad }} -
                {{ $unidad->superficie_m2 }} m2
            </li>
        @empty
            <p>No hay unidades constructivas</p>
        @endforelse
    </ul>
    <a href="{{ route('propiedades.index') }}"> Volver a propiedades</a>
</div>


@endsection