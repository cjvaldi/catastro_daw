@extends('layouts.app')

@section('title', 'Detalle de Propiedad - Catastro DAW')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="card-header" style="margin: 0;">🏠 Detalle de Propiedad</h2>

        {{-- Todos pueden imprimir --}}
        <button onclick="window.print()" class="btn btn-secondary">
            🖨️ Imprimir
        </button>
    </div>

    {{-- Mensajes flash --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Referencia Catastral --}}
    <div class="info-box info-box-blue">
        <strong>Referencia Catastral:</strong>
        <div class="ref-catastral" style="margin-top: 8px;">
            {{ $propiedad->referencia_catastral }}
        </div>
    </div>

    {{-- Localización --}}
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        📍 Localización
    </h3>
    <p style="font-size: 18px; color: #2563eb; margin-bottom: 16px;">
        {{ $propiedad->direccion_text ?? 'N/A' }}
    </p>

    <div class="grid grid-4">
        <div>
            <div class="data-label">Tipo Vía</div>
            <div class="data-value">{{ $propiedad->tipo_via ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Nombre Vía</div>
            <div class="data-value">{{ $propiedad->nombre_via ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Número</div>
            <div class="data-value">{{ $propiedad->numero ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">CP</div>
            <div class="data-value">{{ $propiedad->codigo_postal ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Escalera</div>
            <div class="data-value">{{ $propiedad->escalera ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Planta</div>
            <div class="data-value">{{ $propiedad->planta ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Puerta</div>
            <div class="data-value">{{ $propiedad->puerta ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Datos del Inmueble --}}
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        📊 Datos del Inmueble
    </h3>
    <div class="grid grid-4">
        <div>
            <div class="data-label">Uso</div>
            <div class="data-value">{{ $propiedad->uso ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="data-label">Superficie</div>
            <div class="data-value">{{ $propiedad->superficie_m2 ?? 'N/A' }} m²</div>
        </div>
        <div>
            {{-- usar año de contrucción o antiguedad --}}
            <div class="data-label">Año Construcción</div>
            <div class="data-value">{{ $propiedad->antiguedad_anios ?? 'N/A' }} .</div>
        </div>
        <div>
            <div class="data-label">Provincia/Municipio</div>
            <div class="data-value">{{ $propiedad->provincia }} / {{ $propiedad->municipio }}</div>
        </div>
    </div>

    {{-- Unidades Constructivas --}}
    @if($propiedad->unidadesConstructivas->isNotEmpty())
    <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 18px; font-weight: 600;">
        🏗️ Unidades Constructivas
    </h3>
    @foreach($propiedad->unidadesConstructivas as $unidad)
    <div class="info-box info-box-blue" style="margin-bottom: 8px;">
        <strong>{{ $unidad->tipo_unidad ?? 'N/A' }}</strong> -
        {{ $unidad->tipologia ?? 'N/A' }} -
        {{ $unidad->superficie_m2 ?? 'N/A' }} m²
    </div>
    @endforeach
    @endif

    {{-- SECCIÓN PREMIUM: Favoritos y Notas --}}
    @if(auth()->user()->isPremium())

    {{-- Favorito --}}
    <div style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
        <h3 style="margin-bottom: 16px; font-size: 18px; font-weight: 600;">
            ⭐ Favorito
        </h3>

        @php
        $esFavorito = $propiedad->favoritos()
        ->where('usuario_id', auth()->id())
        ->exists();
        @endphp

        <form method="POST" action="{{ route('propiedades.favorito', $propiedad) }}">
            @csrf
            @if($esFavorito)
            <button type="submit" class="btn btn-warning" style="width: 100%;">
                ⭐ Quitar de Favoritos
            </button>
            @else
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                ☆ Añadir a Favoritos
            </button>
            @endif
        </form>
    </div>

    {{-- Notas --}}
    <div style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
        <h3 style="margin-bottom: 16px; font-size: 18px; font-weight: 600;">
            📝 Notas
        </h3>

        {{-- Formulario añadir nota --}}
        <form method="POST" action="{{ route('propiedades.nota', $propiedad) }}" style="margin-bottom: 24px;">
            @csrf

            <div class="form-group">
                <label class="form-label">Nueva Nota</label>
                <textarea name="contenido"
                    class="form-input"
                    rows="4"
                    placeholder="Escribe tu nota aquí..."
                    required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de Nota</label>
                <select name="tipo" class="form-select" required>
                    <option value="privada">🔒 Privada (solo yo)</option>
                    <option value="publica">🌐 Pública (visible para otros)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                ➕ Añadir Nota
            </button>
        </form>

        {{-- Lista de notas --}}
        @php
        $notas = $propiedad->notas()
        ->where('usuario_id', auth()->id())
        ->latest()
        ->get();
        @endphp

        @if($notas->isEmpty())
        <div class="info-box info-box-yellow">
            <p>No tienes notas para esta propiedad. ¡Añade la primera!</p>
        </div>
        @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($notas as $nota)
            <div class="card" style="background: #f9fafb; padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <div>
                        <span style="font-size: 12px; color: #6b7280;">
                            {{ $nota->created_at->diffForHumans() }}
                        </span>
                        @if($nota->tipo === 'privada')
                        <span style="font-size: 12px; color: #ef4444; margin-left: 8px;">🔒 Privada</span>
                        @else
                        <span style="font-size: 12px; color: #10b981; margin-left: 8px;">🌐 Pública</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('propiedades.nota.eliminar', [$propiedad, $nota]) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('¿Eliminar esta nota?')"
                            style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px;">
                            🗑️
                        </button>
                    </form>
                </div>
                <p style="color: #1f2937; white-space: pre-wrap;">{{ $nota->texto }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @endif

    {{-- Botones de navegación --}}
    <div class="btn-group" style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
        <a href="{{ route('propiedades.index') }}" class="btn btn-secondary">
            ← Volver a Mis Propiedades
        </a>

        @if(!auth()->user()->isPremium())
        <a href="{{ route('upgrade.show') }}" class="btn btn-warning">
            ⭐ Hazte Premium para favoritos y notas
        </a>
        @endif
    </div>
</div>



{{-- CSS para impresión A4 - 1 PÁGINA --}}
<style>
    @media print {

        /* ===== CONFIGURACIÓN PÁGINA ===== */
        @page {
            size: A4 portrait;
            margin: 8mm 12mm;
            /* Márgenes mínimos */
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ===== OCULTAR ELEMENTOS ===== */
        header,
        footer,
        nav,
        .btn,
        .btn-group,
        form,
        button,
        a.btn,
        input,
        select,
        textarea,
        div[style*="border-top: 2px solid"],
        .grid {
            display: none !important;
        }

        /* ===== RESETEAR ESTILOS ===== */
        body {
            background: white !important;
            color: #000 !important;
            font-family: Arial, sans-serif;
            font-size: 7.5pt;
            /* Reducido */
            line-height: 1.2;
            /* Más compacto */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* ===== ENCABEZADO ===== */
        .card::before {
            content: "FICHA CATASTRAL";
            display: block;
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 2mm;
            /* Reducido */
            padding-bottom: 1.5mm;
            border-bottom: 2px solid #000;
        }

        /* ===== TÍTULOS ===== */
        h2 {
            display: none;
            /* Ocultar título duplicado */
        }

        h3 {
            color: #000 !important;
            page-break-after: avoid;
            margin: 2.5mm 0 1.5mm 0;
            /* Reducido */
            font-size: 9.5pt;
            font-weight: bold;
        }

        /* ===== INFO BOXES ===== */
        .info-box {
            border: 1px solid #333 !important;
            background: #f5f5f5 !important;
            padding: 1.5mm !important;
            /* Reducido */
            margin: 1.5mm 0 !important;
            /* Reducido */
            page-break-inside: avoid;
        }

        .ref-catastral {
            background: #e0e0e0 !important;
            border: 1px solid #555 !important;
            padding: 1mm 2mm !important;
            font-family: 'Courier New', monospace;
            font-size: 8.5pt;
            font-weight: bold;
            display: inline-block;
            margin-top: 0.5mm;
        }

        /* ===== TABLAS COMPACTAS ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin: 1mm 0;
        }

        th,
        td {
            border: 0.5pt solid #999;
            padding: 1mm 2mm;
            /* Reducido */
            text-align: left;
            line-height: 1.2;
        }

        th {
            background: #e8e8e8 !important;
            font-weight: bold;
        }

        /* ===== PÁRRAFOS ===== */
        p {
            margin: 0.5mm 0 1.5mm 0;
            /* Reducido */
            font-size: 7.5pt;
        }

        /* ===== PIE DE PÁGINA ===== */
        .card::after {
            content: "Generado: " attr(data-fecha) " | CatastroApp";
            display: block;
            text-align: center;
            font-size: 6.5pt;
            color: #666 !important;
            margin-top: 3mm;
            /* Reducido */
            padding-top: 1.5mm;
            border-top: 0.5pt solid #ccc;
        }

        /* ===== EVITAR SALTOS ===== */
        .info-box,
        table,
        h3 {
            page-break-inside: avoid;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.setAttribute('data-fecha', new Date().toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }));
        });
    });
</script>

@endsection