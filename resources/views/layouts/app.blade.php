<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Catastro DAW' }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    {{-- HEADER UNIFICADO --}}
<header>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>
                <a href="{{ route('home') }}" style="color: white; text-decoration: none;">
                    🏠 Catastro DAW
                </a>
            </h1>
            
            <nav style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                {{-- Menú público --}}
                @guest
                    <a href="{{ route('home') }}">Inicio</a>
                    <a href="{{ route('login') }}">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-warning" style="padding: 6px 16px;">
                        Registrarse
                    </a>
                @endguest

                {{-- Menú autenticado --}}
                @auth
                    {{-- Nombre del usuario (con límite) --}}
                    <span style="color: rgba(255,255,255,0.9); font-weight: 600; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ auth()->user()->name }}">
                        👤 {{ auth()->user()->name }}
                        @if(auth()->user()->isPremium())
                            <span class="badge-premium" style="margin-left: 8px;">⭐</span>
                        @endif
                    </span>

                    {{-- Navegación según rol --}}
                    <a href="{{ route('dashboard') }}">🏠 Inicio</a>
                    <a href="{{ route('propiedades.index') }}">📂 Mis Propiedades</a>
                    <a href="{{ route('propiedades.historial') }}">📊 Historial</a>
                    
                    @if(!auth()->user()->isPremium())
                        <a href="{{ route('upgrade.show') }}" class="btn btn-warning" style="padding: 6px 16px;">
                            ⭐ Premium
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" style="color: #fbbf24;">
                            🔧 Admin
                        </a>
                    @endif

                    {{-- Cerrar sesión --}}
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px;">
                            Salir
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </div>
</header>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="container" style="min-height: calc(100vh - 200px); padding-top: 20px;">
        @yield('content')
    </main>

    {{-- FOOTER UNIFICADO --}}
    <footer>
        <p>&copy; 2026 Catastro DAW - Proyecto Académico - Desarrollado por Cristian J.Valdivieso Valenzuela</p>
        <p style="margin-top: 8px;">
            <a href="{{ route('manual') }}" style="color: #9ca3af; text-decoration: underline;">
                📖 Manual de Uso
            </a>
        </p>
    </footer>

</body>

</html>