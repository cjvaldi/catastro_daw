@extends('layouts.app')

@section('title', 'Manual de Uso - Catastro DAW')

@section('content')
    <div class="container-narrow">
        <div class="card">
            <h2 class="card-header">Guía de Usuario - Catastro DAW</h2>

            <h3 style="margin-top: 24px; margin-bottom: 12px;">1. Búsqueda por Referencia Catastral</h3>
            <p>Introduce la referencia catastral de 14 o 20 caracteres para consultar los datos de una propiedad.</p>

            <h3 style="margin-top: 24px; margin-bottom: 12px;">2. Tipos de Usuario</h3>
            <ul style="list-style: disc; margin-left: 24px; line-height: 1.8;">
                <li><strong>Anónimo:</strong> Búsqueda básica por referencia</li>
                <li><strong>Visitante:</strong> Búsqueda + ver detalles + historial</li>
                <li><strong>Registrado (Premium):</strong> Todo lo anterior + búsqueda por dirección + guardar + favoritos + notas</li>
                <li><strong>Admin:</strong> Control total del sistema</li>
            </ul>

            <h3 style="margin-top: 24px; margin-bottom: 12px;">3. ¿Cómo hacerse Premium?</h3>
            <p>Una vez registrado como Visitante, accede a tu perfil y solicita el upgrade a Premium de forma gratuita.</p>

            <div class="info-box info-box-blue" style="margin-top: 24px;">
                <strong>💡 Más información próximamente</strong>
            </div>
        </div>
    </div>
@endsection