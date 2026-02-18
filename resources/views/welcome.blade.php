@extends('layouts.app')

@section('title', 'Inicio - Catastro DAW')

@section('content')
    {{-- Hero Section --}}
    <div class="card" style="text-align: center; padding: 40px; margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="font-size: 32px; margin-bottom: 16px; color: white;">
            Consulta de Catastro
        </h2>
        <p style="font-size: 18px; opacity: 0.9;">
            Accede a la información catastral de cualquier propiedad en España
        </p>
    </div>

    {{-- Formulario de Búsqueda --}}
    <div class="card">
        <h3 class="card-header">🔍 Búsqueda por Referencia Catastral</h3>

        <div class="info-box info-box-blue">
            <strong>ℹ️ Búsqueda Pública</strong>
            <p>Esta búsqueda está disponible para todos los usuarios, sin necesidad de registro.</p>
        </div>

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

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">
                🔍 Buscar Propiedad
            </button>
        </form>

        {{-- Info adicional --}}
        @guest
        <div class="info-box info-box-yellow" style="margin-top: 24px;">
            <strong>💡 ¿No tienes la referencia catastral?</strong>
            <p style="margin-top: 8px;">
                Los usuarios <strong>Premium</strong> pueden buscar por dirección (calle, número, municipio).
                <a href="{{ route('register') }}" style="color: #2563eb; text-decoration: underline;">
                    Regístrate gratis
                </a> y mejora a Premium.
            </p>
        </div>
        @endguest
    </div>

    {{-- Características --}}
    <div style="margin-top: 40px;">
        <h3 style="text-align: center; margin-bottom: 24px; font-size: 24px;">
            ¿Qué puedes hacer?
        </h3>
        
        <div class="grid grid-3">
            <div class="card">
                <div style="font-size: 48px; text-align: center; margin-bottom: 12px;">🔍</div>
                <h4 style="text-align: center; margin-bottom: 8px;">Búsqueda Rápida</h4>
                <p style="text-align: center; color: #6b7280; font-size: 14px;">
                    Consulta datos catastrales con la referencia
                </p>
            </div>

            <div class="card">
                <div style="font-size: 48px; text-align: center; margin-bottom: 12px;">⭐</div>
                <h4 style="text-align: center; margin-bottom: 8px;">Funciones Premium</h4>
                <p style="text-align: center; color: #6b7280; font-size: 14px;">
                    Búsqueda por dirección, favoritos y notas
                </p>
            </div>

            <div class="card">
                <div style="font-size: 48px; text-align: center; margin-bottom: 12px;">📊</div>
                <h4 style="text-align: center; margin-bottom: 8px;">Historial</h4>
                <p style="text-align: center; color: #6b7280; font-size: 14px;">
                    Guarda y organiza tus búsquedas
                </p>
            </div>
        </div>
    </div>

    {{-- Ejemplo de referencia --}}
    <div class="card" style="margin-top: 40px; background: #f9fafb;">
        <h4 style="margin-bottom: 12px;">📝 Referencia de ejemplo para probar:</h4>
        <div class="ref-catastral" style="display: inline-block;">
            2749704YJ0624N0001DI
        </div>
        <p style="margin-top: 12px; color: #6b7280; font-size: 14px;">
            Copia esta referencia y pégala en el formulario de arriba para ver un ejemplo real.
        </p>
    </div>
@endsection