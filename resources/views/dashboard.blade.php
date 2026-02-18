@extends('layouts.app')

@section('title', 'Mi Panel - Catastro DAW')

@section('content')
    {{-- Bienvenida personalizada --}}
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 30px;">
        <h2 style="font-size: 28px; margin-bottom: 8px; color: white;">
            ¡Bienvenido, {{ auth()->user()->name }}! 👋
        </h2>
        <p style="font-size: 16px; opacity: 0.9;">
            @if(auth()->user()->isPremium())
                Cuenta Premium Activa ⭐
            @else
                Cuenta Visitante
            @endif
        </p>
    </div>

    {{-- BÚSQUEDA POR REFERENCIA CATASTRAL (Todos) --}}
    <div class="card">
        <h3 class="card-header">🔍 Búsqueda por Referencia Catastral</h3>

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('propiedades.buscar') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Referencia Catastral *</label>
                <input type="text" 
                       name="referencia" 
                       class="form-input"
                       placeholder="Ej: 2749704YJ0624N0001DI"
                       minlength="14"
                       maxlength="20"
                       style="font-family: monospace; font-size: 16px;"
                       required>
                <small style="color: #6b7280; display: block; margin-top: 4px;">
                    La referencia catastral tiene entre 14 y 20 caracteres
                </small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                🔍 Buscar por Referencia
            </button>
        </form>
    </div>

    {{-- BÚSQUEDA POR DIRECCIÓN (Solo Premium) --}}
    @if(auth()->user()->isPremium())
        <div class="card">
            <h3 class="card-header">📍 Búsqueda por Dirección (Premium)</h3>

            <form method="POST" action="{{ route('propiedades.buscarDireccion') }}">
                @csrf

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Provincia *</label>
                        <input type="text" 
                               name="provincia" 
                               class="form-input"
                               placeholder="Ej: VALENCIA"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Municipio *</label>
                        <input type="text" 
                               name="municipio" 
                               class="form-input"
                               placeholder="Ej: GODELLETA"
                               required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Tipo de Vía *</label>
                        <select name="tipo_via" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="CL">Calle (CL)</option>
                            <option value="AV">Avenida (AV)</option>
                            <option value="PZ">Plaza (PZ)</option>
                            <option value="PS">Paseo (PS)</option>
                            <option value="CM">Camino (CM)</option>
                            <option value="CR">Carretera (CR)</option>
                            <option value="TR">Travesía (TR)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre de Vía *</label>
                        <input type="text" 
                               name="nombre_via" 
                               class="form-input"
                               placeholder="Ej: MAYOR"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Número (Opcional)</label>
                    <input type="text" 
                           name="numero" 
                           class="form-input"
                           placeholder="Ej: 1, 25, 3B">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    📍 Buscar por Dirección
                </button>
            </form>

            <div class="info-box info-box-blue" style="margin-top: 16px;">
                <strong>💡 Consejo:</strong> Escribe los nombres en mayúsculas sin tildes.
            </div>
        </div>
    @else
        {{-- Visitante: mostrar opción bloqueada --}}
        <div class="card" style="position: relative; overflow: hidden;">
            {{-- Overlay bloqueado --}}
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.9); z-index: 10; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 12px;">🔒</div>
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">
                        Búsqueda por Dirección
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 16px;">
                        Esta función está disponible solo para usuarios Premium
                    </p>
                    <a href="{{ route('upgrade.show') }}" class="btn btn-warning">
                        ⭐ Hazte Premium
                    </a>
                </div>
            </div>

            {{-- Formulario bloqueado (solo visual) --}}
            <h3 class="card-header" style="opacity: 0.5;">📍 Búsqueda por Dirección</h3>
            <div style="opacity: 0.3; pointer-events: none;">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Provincia *</label>
                        <input type="text" class="form-input" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Municipio *</label>
                        <input type="text" class="form-input" disabled>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Tipo de Vía *</label>
                        <select class="form-select" disabled>
                            <option>Selecciona...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nombre de Vía *</label>
                        <input type="text" class="form-input" disabled>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Accesos rápidos --}}
    <div class="card">
        <h3 class="card-header">⚡ Accesos Rápidos</h3>

        <div class="grid grid-3">
            <a href="{{ route('propiedades.index') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px; transition: transform 0.2s;">
                <div style="font-size: 48px; margin-bottom: 12px;">📂</div>
                <h4 style="margin-bottom: 8px; color: #1f2937;">Mis Propiedades</h4>
                <p style="color: #6b7280; font-size: 14px;">Ver propiedades guardadas</p>
            </a>

            <a href="{{ route('propiedades.historial') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px; transition: transform 0.2s;">
                <div style="font-size: 48px; margin-bottom: 12px;">📊</div>
                <h4 style="margin-bottom: 8px; color: #1f2937;">Historial</h4>
                <p style="color: #6b7280; font-size: 14px;">Ver búsquedas anteriores</p>
            </a>

            @if(auth()->user()->isPremium())
                <a href="{{ route('propiedades.formBuscarDireccion') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px; transition: transform 0.2s;">
                    <div style="font-size: 48px; margin-bottom: 12px;">🔍</div>
                    <h4 style="margin-bottom: 8px; color: #1f2937;">Búsqueda Avanzada</h4>
                    <p style="color: #6b7280; font-size: 14px;">Buscar por dirección</p>
                </a>
            @else
                <a href="{{ route('upgrade.show') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                    <div style="font-size: 48px; margin-bottom: 12px;">⭐</div>
                    <h4 style="margin-bottom: 8px; color: white;">Hazte Premium</h4>
                    <p style="color: rgba(255,255,255,0.9); font-size: 14px;">Desbloquea más funciones</p>
                </a>
            @endif
        </div>
    </div>
@endsection