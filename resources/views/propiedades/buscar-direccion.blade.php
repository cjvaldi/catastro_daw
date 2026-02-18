@extends('layouts.app')

@section('title', 'Búsqueda por Dirección - Catastro DAW')

@section('content')
    <div class="container-narrow">
        <div class="card">
            <h2 class="card-header">🔍 Búsqueda por Dirección</h2>

            <div class="info-box info-box-yellow">
                <strong>⭐ Función Premium</strong>
                <p>Esta búsqueda avanzada está disponible solo para usuarios Premium.</p>
            </div>

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('propiedades.buscarDireccion') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Provincia *</label>
                    <input type="text" 
                           name="provincia" 
                           class="form-input"
                           value="{{ old('provincia') }}"
                           placeholder="Ej: VALENCIA, SEVILLA, MADRID"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Municipio *</label>
                    <input type="text" 
                           name="municipio" 
                           class="form-input"
                           value="{{ old('municipio') }}"
                           placeholder="Ej: GODELLETA, SAN JUAN"
                           required>
                </div>

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
                    <label class="form-label">Nombre de la Vía *</label>
                    <input type="text" 
                           name="nombre_via" 
                           class="form-input"
                           value="{{ old('nombre_via') }}"
                           placeholder="Ej: MAYOR, CONSTITUCION"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Número (Opcional)</label>
                    <input type="text" 
                           name="numero" 
                           class="form-input"
                           value="{{ old('numero') }}"
                           placeholder="Ej: 1, 25, 3B">
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        🔍 Buscar
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>

            <div class="info-box info-box-blue" style="margin-top: 24px;">
                <strong>💡 Consejo:</strong> Escribe los nombres en mayúsculas sin tildes para mejores resultados.
            </div>
        </div>
    </div>
@endsection