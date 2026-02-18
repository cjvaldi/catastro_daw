@extends('layouts.app')

@section('title', 'Hazte Premium - Catastro DAW')

@section('content')
    <div class="container-narrow">
        <div class="card" style="text-align: center; padding: 40px;">
            
            {{-- Icono --}}
            <div style="font-size: 80px; margin-bottom: 16px;">⭐</div>

            <h2 style="font-size: 32px; margin-bottom: 16px;">
                Hazte Premium
            </h2>
            <p style="font-size: 18px; color: #6b7280; margin-bottom: 32px;">
                Desbloquea todas las funcionalidades de CatastroApp
            </p>

            {{-- Comparativa --}}
            <div class="grid grid-2" style="text-align: left; margin-bottom: 32px; gap: 24px;">
                <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 24px;">
                    <h3 style="font-size: 20px; margin-bottom: 16px; color: #6b7280;">
                        🆓 Visitante (Actual)
                    </h3>
                    <ul style="list-style: none; padding: 0; line-height: 2;">
                        <li>✅ Buscar por referencia</li>
                        <li>✅ Ver detalles</li>
                        <li>✅ Guardar propiedades</li>
                        <li>✅ Historial de búsquedas</li>
                        <li>❌ Búsqueda por dirección</li>
                        <li>❌ Favoritos</li>
                        <li>❌ Notas</li>
                    </ul>
                </div>

                <div style="border: 3px solid #fbbf24; border-radius: 8px; padding: 24px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                    <h3 style="font-size: 20px; margin-bottom: 16px; color: #92400e;">
                        ⭐ Premium
                    </h3>
                    <ul style="list-style: none; padding: 0; line-height: 2;">
                        <li>✅ Todo lo de Visitante</li>
                        <li>✅ <strong>Búsqueda por dirección</strong></li>
                        <li>✅ <strong>Favoritos</strong></li>
                        <li>✅ <strong>Notas privadas y públicas</strong></li>
                        <li>✅ <strong>Exportar PDF</strong></li>
                        <li>✅ <strong>Soporte prioritario</strong></li>
                    </ul>
                </div>
            </div>

            {{-- Precio --}}
            <div class="info-box info-box-yellow" style="margin-bottom: 24px;">
                <p style="font-size: 24px; font-weight: 700; margin: 0;">
                    🎉 GRATIS durante el período académico
                </p>
            </div>

            {{-- Botón upgrade --}}
            <form method="POST" action="{{ route('upgrade.process') }}">
                @csrf
                <button type="submit" class="btn btn-warning" style="font-size: 18px; padding: 16px 48px; width: 100%;">
                    ⭐ Activar Premium GRATIS
                </button>
            </form>

            <p style="margin-top: 16px; font-size: 12px; color: #9ca3af;">
                Simulación académica - Sin cobro real
            </p>

            <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="margin-top: 24px;">
                Tal vez más tarde
            </a>
        </div>
    </div>
@endsection