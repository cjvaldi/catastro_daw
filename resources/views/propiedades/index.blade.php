@extends('layouts.app')

@section('title', 'Mis Propiedades - Catastro DAW')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="card-header" style="margin: 0;">📂 Mis Propiedades Guardadas</h2>
            
            {{-- Filtro de favoritas (solo Premium) --}}
            @if(auth()->user()->isPremium())
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('propiedades.index') }}" 
                       class="btn {{ !request('filtro') ? 'btn-primary' : 'btn-secondary' }}"
                       style="padding: 8px 16px;">
                        📂 Todas
                    </a>
                    <a href="{{ route('propiedades.index', ['filtro' => 'favoritas']) }}" 
                       class="btn {{ request('filtro') === 'favoritas' ? 'btn-primary' : 'btn-secondary' }}"
                       style="padding: 8px 16px;">
                        ⭐ Favoritas
                    </a>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($propiedades->isEmpty())
            <div class="info-box info-box-yellow">
                <strong>📭 
                    @if(request('filtro') === 'favoritas')
                        No tienes propiedades marcadas como favoritas
                    @else
                        No tienes propiedades guardadas
                    @endif
                </strong>
                <p style="margin-top: 8px;">
                    @if(request('filtro') === 'favoritas')
                        Marca alguna propiedad como favorita para verla aquí.
                    @else
                        Busca una propiedad y guárdala para verla aquí.
                    @endif
                </p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top: 12px;">
                    🔍 Ir a Búsqueda
                </a>
            </div>
        @else
            <div style="margin-bottom: 16px;">
                <p style="color: #6b7280;">
                    @if(request('filtro') === 'favoritas')
                        Mostrando <strong>{{ $propiedades->total() }}</strong> propiedades favoritas
                    @else
                        Total: <strong>{{ $propiedades->total() }}</strong> propiedades guardadas
                    @endif
                </p>
            </div>

            {{-- Lista de propiedades --}}
            <div class="space-y-4" style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($propiedades as $propiedad)
                    <div class="result-item">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                {{-- Título con indicador de favorito --}}
                                <h3 class="result-header" style="display: flex; align-items: center; gap: 8px;">
                                    {{ $propiedad->direccion_text ?? 'Dirección no disponible' }}
                                    
                                    @if(auth()->user()->isPremium())
                                        @php
                                            $esFavorito = $propiedad->favoritos()
                                                ->where('usuario_id', auth()->id())
                                                ->exists();
                                        @endphp
                                        
                                        @if($esFavorito)
                                            <span style="color: #fbbf24; font-size: 20px;" title="Favorito">⭐</span>
                                        @endif
                                    @endif
                                </h3>
                                
                                <div class="grid grid-4" style="margin-top: 12px;">
                                    <div>
                                        <div class="data-label">Ref. Catastral</div>
                                        <div class="ref-catastral" style="font-size: 12px; padding: 4px 8px;">
                                            {{ $propiedad->referencia_catastral }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="data-label">Uso</div>
                                        <div class="data-value">{{ $propiedad->uso ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="data-label">Superficie</div>
                                        <div class="data-value">{{ $propiedad->superficie_m2 ?? 'N/A' }} m²</div>
                                    </div>
                                    <div>
                                        <div class="data-label">Año Construcción</div>
                                        <div class="data-value">{{ $propiedad->antiguedad_anios ?? 'N/A' }} .</div>
                                    </div>
                                </div>

                                <div style="margin-top: 12px; color: #6b7280; font-size: 14px;">
                                    📍 {{ $propiedad->provincia }} / {{ $propiedad->municipio }}
                                </div>
                            </div>

                            <div style="margin-left: 16px; display: flex; flex-direction: column; gap: 8px;">
                                <a href="{{ route('propiedades.show', $propiedad) }}" 
                                   class="btn btn-primary" 
                                   style="white-space: nowrap;">
                                    Ver Detalle →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div style="margin-top: 24px;">
                {{ $propiedades->appends(['filtro' => request('filtro')])->links() }}
            </div>
        @endif
    </div>
@endsection