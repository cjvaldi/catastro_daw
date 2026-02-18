@extends('layouts.app')

@section('title', 'Historial de Búsquedas - Catastro DAW')

@section('content')
<div class="card">
    <h2 class="card-header">📊 Historial de Búsquedas</h2>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="info-box info-box-blue">
        <strong>ℹ️ Tu historial de búsquedas</strong>
        <p>Aquí puedes ver todas las búsquedas que has realizado en el Catastro.</p>
    </div>

    @if($busquedas->isEmpty())
    <div class="info-box info-box-yellow">
        <strong>📭 No tienes búsquedas registradas</strong>
        <p style="margin-top: 8px;">
            Realiza una búsqueda para que aparezca en tu historial.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 12px;">
            🔍 Ir a Búsqueda
        </a>
    </div>
    @else
    <div style="margin-bottom: 16px;">
        <p style="color: #6b7280;">
            Total: <strong>{{ $busquedas->total() }}</strong> búsquedas realizadas
        </p>
    </div>

    {{-- Lista de búsquedas --}}
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($busquedas as $busqueda)
        <div class="card" style="background: #f9fafb; padding: 16px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    {{-- Fecha y hora --}}
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">
                        🕐 {{ $busqueda->created_at->format('d/m/Y H:i') }}
                        <span style="margin-left: 8px; color: #9ca3af;">
                            ({{ $busqueda->created_at->diffForHumans() }})
                        </span>
                    </div>

                    {{-- Texto de búsqueda --}}
                    <div style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">
                        @php
                        $params = is_array($busqueda->params_json)
                        ? $busqueda->params_json
                        : json_decode($busqueda->params_json, true);
                        $tipo = $params['tipo'] ?? 'referencia';
                        @endphp

                        @if($tipo === 'referencia')
                        🔍 Búsqueda por Referencia
                        @else
                        📍 Búsqueda por Dirección
                        @endif
                    </div>

                    {{-- Detalle de la búsqueda --}}
                    <div style="color: #4b5563;">
                        @if($tipo === 'referencia')
                        <span class="ref-catastral" style="font-size: 12px; padding: 4px 8px;">
                            {{ $busqueda->query_text }}
                        </span>
                        @else
                        📍 {{ $busqueda->query_text }}
                        @endif
                    </div>

                    {{-- Resultados --}}
                    @if($busqueda->result_count > 0)
                    <div style="margin-top: 8px; font-size: 14px; color: #059669;">
                        ✅ {{ $busqueda->result_count }} resultado(s) encontrado(s)
                    </div>
                    @else
                    <div style="margin-top: 8px; font-size: 14px; color: #dc2626;">
                        ❌ Sin resultados
                    </div>
                    @endif
                </div>

                {{-- Botón repetir búsqueda --}}
                <div style="margin-left: 16px;">
                    @if($tipo === 'referencia')
                    <form method="POST" action="{{ route('propiedades.buscar') }}">
                        @csrf
                        <input type="hidden" name="referencia" value="{{ $busqueda->query_text }}">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                            🔄 Repetir
                        </button>
                    </form>
                    @else
                    <a href="{{ route('propiedades.formBuscarDireccion') }}"
                        class="btn btn-primary"
                        style="white-space: nowrap;">
                        🔄 Nueva búsqueda
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    {{-- Paginación personalizada --}}
    @if($busquedas->hasPages())
    <div style="margin-top: 24px;">
        <nav style="display: flex; justify-content: center; align-items: center; gap: 8px;">
            {{-- Anterior --}}
            @if($busquedas->onFirstPage())
            <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                ← Anterior
            </span>
            @else
            <a href="{{ $busquedas->previousPageUrl() }}" class="btn btn-secondary">
                ← Anterior
            </a>
            @endif

            {{-- Números de página --}}
            @foreach(range(1, $busquedas->lastPage()) as $page)
            @if($page == $busquedas->currentPage())
            <span class="btn btn-primary" style="cursor: default;">
                {{ $page }}
            </span>
            @else
            <a href="{{ $busquedas->url($page) }}" class="btn btn-secondary">
                {{ $page }}
            </a>
            @endif
            @endforeach

            {{-- Siguiente --}}
            @if($busquedas->hasMorePages())
            <a href="{{ $busquedas->nextPageUrl() }}" class="btn btn-secondary">
                Siguiente →
            </a>
            @else
            <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                Siguiente →
            </span>
            @endif
        </nav>

        <div style="text-align: center; margin-top: 12px; color: #6b7280; font-size: 14px;">
            Mostrando {{ $busquedas->firstItem() }} - {{ $busquedas->lastItem() }} de {{ $busquedas->total() }} búsquedas
        </div>
    </div>
    @endif
    @endif

    {{-- Botón volver --}}
    <div class="btn-group" style="margin-top: 24px;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            ← Volver al Dashboard
        </a>
    </div>
</div>
@endsection