<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa - Catastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        
        <h2 class="text-2xl font-bold mb-6">Vista Previa de Propiedad</h2>

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

        {{-- Referencia --}}
        <div class="mb-6 p-4 bg-blue-50 rounded">
            <p class="text-sm text-gray-600">Referencia Catastral</p>
            <p class="text-xl font-mono font-bold">{{ $referencia }}</p>
        </div>

        {{-- Dirección --}}
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-3">📍 Localización</h3>
            <p class="text-lg text-blue-600 mb-3">{{ $bi['ldt'] ?? 'N/A' }}</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Tipo Vía:</span>
                    <p class="font-semibold">{{ $dir['tv'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Nombre Vía:</span>
                    <p class="font-semibold">{{ $dir['nv'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Número:</span>
                    <p class="font-semibold">{{ $dir['pnp'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">CP:</span>
                    <p class="font-semibold">{{ $lourb['dp'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Escalera:</span>
                    <p class="font-semibold">{{ $loint['es'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Planta:</span>
                    <p class="font-semibold">{{ $loint['pt'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Puerta:</span>
                    <p class="font-semibold">{{ $loint['pu'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Datos del Inmueble --}}
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-3">📊 Datos del Inmueble</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Uso:</span>
                    <p class="font-semibold">{{ $debi['luso'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Superficie:</span>
                    <p class="font-semibold">{{ $debi['sfc'] ?? 'N/A' }} m²</p>
                </div>
                <div>
                    <span class="text-gray-500">Antigüedad:</span>
                    <p class="font-semibold">{{ $debi['ant'] ?? 'N/A' }} años</p>
                </div>
                <div>
                    <span class="text-gray-500">Provincia/Municipio:</span>
                    <p class="font-semibold">{{ $dt['np'] ?? 'N/A' }} / {{ $dt['nm'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="mt-6 flex gap-4">
            <a href="{{ url('/') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Volver
            </a>

            @auth
                @if(auth()->user()->isPremium())
                    <form method="POST" action="{{ route('propiedades.guardar') }}">
                        @csrf
                        <input type="hidden" name="referencia" value="{{ $referencia }}">
                        <input type="hidden" name="raw_json" value="{{ json_encode($datos) }}">
                        
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            💾 Guardar Propiedad
                        </button>
                    </form>
                @else
                    <a href="{{ route('upgrade.show') }}"
                       class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded">
                        ⭐ Hazte Premium para guardar
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Inicia sesión para guardar
                </a>
            @endauth
        </div>

        {{-- DEBUG: Mostrar datos de dirección --}}
        <div class="mt-6 p-4 bg-gray-100 rounded text-xs">
            <p class="font-mono"><strong>DEBUG dir:</strong> {{ json_encode($dir) }}</p>
            <p class="font-mono"><strong>DEBUG loint:</strong> {{ json_encode($loint) }}</p>
        </div>

    </div>
</div>

</body>
</html>