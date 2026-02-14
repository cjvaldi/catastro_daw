@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Propiedades</h1>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Referencia</th>
                <th>Provincia</th>
                <th>Municipio</th>
                <th>Uso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($propiedades as $propiedad)
            <tr>
                <td>{{ $propiedad->referencia_catastral }}</td>
                <td>{{ $propiedad->provincia }}</td>
                <td>{{ $propiedad->municipio }}</td>
                <td>{{ $propiedad->uso }}</td>
                <td>
                    <a href="{{ route('propiedades.show',$propiedad) }}"> Ver </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No hay propiedades registradas</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $propiedades->links() }}
</div>

@endsection