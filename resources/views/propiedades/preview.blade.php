@extends('layouts.app')

@section('title', 'Vista Previa - Catastro DAW')

@section('content')
<div class="card">
    <h2 class="card-header">Vista Previa de Propiedad</h2>

    @php
    $bico = $datos['consulta_dnprcResult']['bico'] ?? [];
    $bi = $bico['bi'] ?? [];
    $dt = $bi['dt'] ?? [];
    $debi = $bi['debi'] ?? [];
    $idbi = $bi['idbi'] ?? [];
    $lourb = $dt['locs']['lous']['lourb'] ?? [];
    $dir = $lourb['dir'] ?? [];
    $loint = $lourb['loint'] ?? [];
    @endphp

    {{-- Referencia Catastral --}}
    <div class="info-box info-box-blue">
        <strong>Referencia Catastral:</strong>
        <div class="ref-catastral" style="margin-top: 8px;">{{ $referencia }}</div>
    </div>

    {{-- Localización --}}
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        📍 Localización
    </h3>
    <p style="font-size: 18px; color: #2563eb; margin-bottom: 16px;">
        {{ $bi['ldt'] ?? 'N/A' }}
    </p>

    <div class="grid grid-4">
        <div>
            <div class="data-label">Tipo Vía</div>
            <div class="data-value">{{ $dir['tv'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Nombre Vía</div>
            <div class="data-value">{{ $dir['nv'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Número</div>
            <div class="data-value">{{ $dir['pnp'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">CP</div>
            <div class="data-value">{{ $lourb['dp'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Escalera</div>
            <div class="data-value">{{ $loint['es'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Planta</div>
            <div class="data-value">{{ $loint['pt'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Puerta</div>
            <div class="data-value">{{ $loint['pu'] ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Datos del Inmueble --}}
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        📊 Datos del Inmueble
    </h3>
    <div class="grid grid-4">
        <div>
            <div class="data-label">Uso</div>
            <div class="data-value">{{ $debi['luso'] ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Superficie</div>
            <div class="data-value">{{ $debi['sfc'] ?? 'N/A' }} m²</div>
        </div>
        <div>
            <div class="data-label">Año Construcción</div>
            <div class="data-value">{{ $debi['ant'] ?? 'N/A' }} .</div>
        </div>
        <div>
            <div class="data-label">Provincia/Municipio</div>
            <div class="data-value">{{ $dt['np'] ?? 'N/A' }} / {{ $dt['nm'] ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Unidades Constructivas --}}
    @if(isset($bico['lcons']) && is_array($bico['lcons']))
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        🏗️ Unidades Constructivas
    </h3>
    @foreach($bico['lcons'] as $unidad)
    <div class="info-box info-box-blue" style="margin-bottom: 8px;">
        <strong>{{ $unidad['lcd'] ?? 'N/A' }}</strong> -
        {{ $unidad['dvcons']['dtip'] ?? 'N/A' }} -
        {{ $unidad['dfcons']['stl'] ?? 'N/A' }} m²
    </div>
    @endforeach
    @endif

    {{-- Botones --}}
    <div class="btn-group">
        <a href="{{ route('home') }}" class="btn btn-secondary">← Volver</a>

        @auth
        {{-- TODOS los autenticados pueden guardar --}}
        <form method="POST" action="{{ route('propiedades.guardar') }}">
            @csrf
            <input type="hidden" name="referencia" value="{{ $referencia }}">
            <input type="hidden" name="raw_json" value="{{ json_encode($datos) }}">

            <button type="submit" class="btn btn-primary">
                💾 Guardar Propiedad
            </button>
        </form>

        {{-- Solo Premium: favoritos y notas desde la vista de detalle --}}
        @if(!auth()->user()->isPremium())
        <a href="{{ route('upgrade.show') }}" class="btn btn-warning">
            ⭐ Hazte Premium para favoritos y notas
        </a>
        @endif
        @else
        <a href="{{ route('login') }}" class="btn btn-success">
            Inicia sesión para guardar
        </a>
        @endauth
    </div>
</div>
@endsection