<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Catastro DAW' }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">

    <div style="width: 100%; max-width: 450px;">
        {{-- Logo y título --}}
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 64px; margin-bottom: 12px;">🏠</div>
            <h1 style="color: white; font-size: 32px; font-weight: bold; margin-bottom: 4px;">
                Catastro DAW
            </h1>
            <p style="color: rgba(255,255,255,0.9); font-size: 16px;">
                Sistema de Consulta Catastral
            </p>
        </div>

        {{-- Contenido del formulario --}}
        <div class="card" style="padding: 32px;">
            {{ $slot }}
        </div>

        {{-- Enlace a inicio --}}
        <div style="text-align: center; margin-top: 16px;">
            <a href="{{ route('home') }}" style="color: white; text-decoration: none; font-size: 14px;">
                ← Volver al inicio
            </a>
        </div>

        {{-- Footer --}}
        <div style="text-align: center; margin-top: 24px; color: rgba(255,255,255,0.7); font-size: 12px;">
            <p>&copy; 2026 Catastro DAW - Proyecto Académico - Desarrollado por Cristian J.Valdivieso Valenzuela</p>
            <p style="margin-top: 4px;">
                Contacto: <a href="mailto:cj_valdi@hotmail.com" style="color: rgba(255,255,255,0.9); text-decoration: none;">cj_valdi@hotmail.com</a>
            </p>
        </div>
    </div>

</body>

</html>