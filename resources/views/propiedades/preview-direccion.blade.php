<x-app-layout>
    <div class="card">
        <h2 class="card-header">Resultados de Búsqueda por Dirección</h2>

        @php
            $result = $datos['consulta_dnplocResult'] ?? [];
            $bicos = $result['bico'] ?? [];
            
            if (isset($bicos['bi'])) {
                $bicos = [$bicos];
            }
        @endphp

        <div class="info-box info-box-blue">
            <strong>Búsqueda:</strong> 
            {{ $filtro['tipo_via'] }} {{ $filtro['nombre_via'] }} 
            {{ $filtro['numero'] ?? '' }}, 
            {{ $filtro['municipio'] }} ({{ $filtro['provincia'] }})
            <br>
            <strong>Resultados encontrados:</strong> {{ count($bicos) }}
        </div>

        @if(empty($bicos))
            <div class="alert alert-error">
                No se encontraron inmuebles con esa dirección.
            </div>
            <a href="{{ route('propiedades.formBuscarDireccion') }}" class="btn btn-primary">
                ← Nueva búsqueda
            </a>
        @else
            @foreach($bicos as $bico)
                @php
                    $bi = $bico['bi'] ?? [];
                    $rc = $bi['idbi']['rc'] ?? [];
                    $refCat = ($rc['pc1'] ?? '') . ($rc['pc2'] ?? '') . 
                              ($rc['car'] ?? '') . ($rc['cc1'] ?? '') . ($rc['cc2'] ?? '');
                    $debi = $bi['debi'] ?? [];
                @endphp

                <div class="result-item">
                    <h3 class="result-header">
                        {{ $bi['ldt'] ?? 'Dirección no disponible' }}
                    </h3>
                    
                    <div class="grid grid-4">
                        <div>
                            <div class="data-label">Ref. Catastral</div>
                            <div class="ref-catastral">{{ $refCat }}</div>
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

                    <form method="POST" action="{{ route('propiedades.buscar') }}" style="margin-top: 16px;">
                        @csrf
                        <input type="hidden" name="referencia" value="{{ $refCat }}">
                        <button type="submit" class="btn btn-primary">
                            Ver detalle →
                        </button>
                    </form>
                </div>
            @endforeach

            <a href="{{ route('propiedades.formBuscarDireccion') }}" class="btn btn-secondary" style="margin-top: 20px;">
                ← Nueva búsqueda
            </a>
        @endif
    </div>
</x-app-layout>