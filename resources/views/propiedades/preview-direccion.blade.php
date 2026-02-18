@extends('layouts.app')

@section('title', 'Resultados por Dirección - Catastro DAW')

@section('content')
<div class="card">
    <h2 class="card-header">📍 Resultados de Búsqueda por Dirección</h2>

    {{-- Aviso de datos simulados --}}
    @if(isset($simulado) && $simulado)
    <div class="info-box info-box-yellow">
        <strong>⚠️ Modo Demostración</strong>
        <p style="margin-top: 8px;">
            La API pública del Catastro tiene limitaciones en la búsqueda por dirección.
            Se muestran <strong>propiedades demo con datos de la busqueda(2) y de ejemplo reales(2)</strong> con fines demostrativos.
            Puedes ver sus detalles completos haciendo clic en "Ver Detalle".
        </p>
    </div>
    @endif

    @php
    $result = $datos['consulta_dnplocResult'] ?? [];
    $bicos = $result['bico'] ?? [];

    // Si bico es un objeto único, convertirlo en array
    if (isset($bicos['bi'])) {
    $bicos = [$bicos];
    }
    @endphp

    {{-- Filtro aplicado --}}
    <div class="info-box info-box-blue">
        <strong>Búsqueda:</strong>
        {{ $filtro['tipo_via'] }} {{ $filtro['nombre_via'] }}
        {{ $filtro['numero'] ?? '' }},
        {{ $filtro['municipio'] }} ({{ $filtro['provincia'] }})
        <br>
        <strong>Resultados encontrados:</strong> {{ count($bicos) }}
    </div>

    @if(empty($bicos))
    <div class="info-box info-box-yellow">
        <strong>📭 No se encontraron inmuebles</strong>
        <p style="margin-top: 8px;">
            No se encontraron propiedades con esa dirección.
            Verifica que los datos sean correctos.
        </p>
    </div>
    <a href="{{ route('propiedades.formBuscarDireccion') }}" class="btn btn-primary">
        ← Nueva búsqueda
    </a>
    @else
    {{-- Lista de resultados --}}
    <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 20px;">
        @foreach($bicos as $bico)
        @php
        $bi = $bico['bi'] ?? [];
        $rc = $bi['idbi']['rc'] ?? [];
        $refCat = ($rc['pc1'] ?? '') . ($rc['pc2'] ?? '') .
        ($rc['car'] ?? '') . ($rc['cc1'] ?? '') . ($rc['cc2'] ?? '');
        $debi = $bi['debi'] ?? [];
        $esSimulado = $bi['_simulado'] ?? false; //  Verificar flag de ejemplo o demo
        @endphp

        <div class="result-item">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <h3 class="result-header">
                        {{ $bi['ldt'] ?? 'Dirección no disponible' }}

                        {{-- Badge simulado --}}
                        @if($esSimulado)
                        <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-left: 8px;">
                            📋 Simulado
                        </span>
                        @endif
                    </h3>

                    <div class="grid grid-4" style="margin-top: 12px;">
                        <div>
                            <div class="data-label">Ref. Catastral</div>
                            <div class="ref-catastral" style="font-size: 12px; padding: 4px 8px;">
                                {{ $refCat }}
                            </div>
                        </div>
                        <div>
                            <div class="data-label">Uso</div>
                            <div class="data-value">{{ $debi['luso'] ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="data-label">Superficie</div>
                            <div class="data-value">{{ $debi['sfc'] ?? 'N/A' }} m²</div>
                        </div>
                        <div>
                            <div class="data-label">Antigüedad</div>
                            <div class="data-value">{{ $debi['ant'] ?? 'N/A' }} años</div>
                        </div>
                    </div>
                </div>

                <div style="margin-left: 16px;">
                    @if($esSimulado)
                    {{-- Si es simulado, NO mostrar botón --}}
                    <span style="color: #6b7280; font-size: 14px; font-style: italic;">
                        Datos de ejemplo
                    </span>
                    @else
                    {{-- Si es real, mostrar botón Ver Detalle --}}
                    <form method="POST" action="{{ route('propiedades.buscar') }}">
                        @csrf
                        <input type="hidden" name="referencia" value="{{ $refCat }}">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                            Ver Detalle →
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <a href="{{ route('propiedades.formBuscarDireccion') }}"
        class="btn btn-secondary"
        style="margin-top: 24px;">
        ← Nueva búsqueda
    </a>
    @endif
</div>
@endsection