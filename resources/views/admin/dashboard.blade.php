@extends('layouts.app')

@section('title', 'Panel de Administración - Catastro DAW')

@section('content')
    <div class="card">
        <h2 class="card-header">🔧 Panel de Administración</h2>

        <div class="info-box info-box-blue">
            <strong>👋 Bienvenido, Admin</strong>
            <p>Desde aquí puedes gestionar usuarios, ver estadísticas y logs del sistema.</p>
        </div>

        {{-- Estadísticas --}}
        <h3 style="margin-top: 24px; margin-bottom: 16px; font-size: 18px; font-weight: 600;">
            📊 Estadísticas del Sistema
        </h3>

        <div class="grid grid-4">
            <div class="card" style="text-align: center; background: #eff6ff;">
                <div style="font-size: 36px; font-weight: bold; color: #1e40af; margin-bottom: 8px;">
                    {{ $stats['total_usuarios'] }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Usuarios Totales</div>
            </div>

            <div class="card" style="text-align: center; background: #fef3c7;">
                <div style="font-size: 36px; font-weight: bold; color: #92400e; margin-bottom: 8px;">
                    {{ $stats['usuarios_premium'] }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Usuarios Premium</div>
            </div>

            <div class="card" style="text-align: center; background: #ecfdf5;">
                <div style="font-size: 36px; font-weight: bold; color: #065f46; margin-bottom: 8px;">
                    {{ $stats['propiedades_guardadas'] }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Propiedades Guardadas</div>
            </div>

            <div class="card" style="text-align: center; background: #f3e8ff;">
                <div style="font-size: 36px; font-weight: bold; color: #6b21a8; margin-bottom: 8px;">
                    {{ $stats['busquedas_realizadas'] }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Búsquedas Realizadas</div>
            </div>
        </div>

        {{-- Accesos rápidos Admin --}}
        <h3 style="margin-top: 32px; margin-bottom: 16px; font-size: 18px; font-weight: 600;">
            ⚡ Gestión Rápida
        </h3>

        <div class="grid grid-3">
            <a href="{{ route('admin.usuarios') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">👥</div>
                <h4 style="margin-bottom: 8px; color: #1f2937;">Gestionar Usuarios</h4>
                <p style="color: #6b7280; font-size: 14px;">Ver, editar y cambiar roles</p>
            </a>

            <a href="{{ route('admin.logs') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">📋</div>
                <h4 style="margin-bottom: 8px; color: #1f2937;">Logs de API</h4>
                <p style="color: #6b7280; font-size: 14px;">Monitorear llamadas API</p>
            </a>

            <a href="{{ route('dashboard') }}" class="card" style="text-decoration: none; text-align: center; padding: 24px; background: #f9fafb;">
                <div style="font-size: 48px; margin-bottom: 12px;">🏠</div>
                <h4 style="margin-bottom: 8px; color: #1f2937;">Volver al Inicio</h4>
                <p style="color: #6b7280; font-size: 14px;">Panel de usuario</p>
            </a>
        </div>
    </div>
@endsection