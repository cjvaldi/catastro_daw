@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
    <div class="card">
        <h2 class="card-header">👥 Gestión de Usuarios</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div style="margin-bottom: 16px;">
            <p style="color: #6b7280;">
                Total: <strong>{{ $usuarios->total() }}</strong> usuarios registrados
            </p>
        </div>

        {{-- Tabla de usuarios --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">ID</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Nombre</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Email</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Rol</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Estado</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Registro</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px;">{{ $usuario->id }}</td>
                        <td style="padding: 12px; font-weight: 600;">{{ $usuario->name }}</td>
                        <td style="padding: 12px;">{{ $usuario->email }}</td>
                        <td style="padding: 12px;">
                            @if($usuario->rol === 'admin')
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    🔧 Admin
                                </span>
                            @elseif($usuario->rol === 'registrado')
                                <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    ⭐ Premium
                                </span>
                            @else
                                <span style="background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    👤 Visitante
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if($usuario->activo)
                                <span style="color: #059669; font-weight: 600;">✅ Activo</span>
                            @else
                                <span style="color: #dc2626; font-weight: 600;">❌ Inactivo</span>
                            @endif
                        </td>
                        <td style="padding: 12px; font-size: 14px; color: #6b7280;">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td style="padding: 12px;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                {{-- Cambiar rol (solo si no es admin) --}}
                                @if($usuario->rol !== 'admin')
                                    <form method="POST" action="{{ route('admin.usuarios.rol', $usuario) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-primary" 
                                                style="padding: 6px 12px; font-size: 12px; white-space: nowrap;">
                                            @if($usuario->rol === 'visitante')
                                                ⭐ A Premium
                                            @else
                                                👤 A Visitante
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Activar/Desactivar --}}
                                    <form method="POST" action="{{ route('admin.usuarios.toggle', $usuario) }}">
                                        @csrf
                                        <button type="submit" 
                                                class="btn {{ $usuario->activo ? 'btn-secondary' : 'btn-success' }}" 
                                                style="padding: 6px 12px; font-size: 12px;"
                                                onclick="return confirm('¿Cambiar estado del usuario?')">
                                            @if($usuario->activo)
                                                🔒 Desactivar
                                            @else
                                                ✅ Activar
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #9ca3af; font-size: 12px;">Admin</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
            <div style="margin-top: 24px;">
                <nav style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                    @if($usuarios->onFirstPage())
                        <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                            ← Anterior
                        </span>
                    @else
                        <a href="{{ $usuarios->previousPageUrl() }}" class="btn btn-secondary">
                            ← Anterior
                        </a>
                    @endif

                    @foreach(range(1, $usuarios->lastPage()) as $page)
                        @if($page == $usuarios->currentPage())
                            <span class="btn btn-primary" style="cursor: default;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $usuarios->url($page) }}" class="btn btn-secondary">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($usuarios->hasMorePages())
                        <a href="{{ $usuarios->nextPageUrl() }}" class="btn btn-secondary">
                            Siguiente →
                        </a>
                    @else
                        <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                            Siguiente →
                        </span>
                    @endif
                </nav>
            </div>
        @endif

        <div class="btn-group" style="margin-top: 24px;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                ← Volver al Panel Admin
            </a>
        </div>
    </div>
@endsection