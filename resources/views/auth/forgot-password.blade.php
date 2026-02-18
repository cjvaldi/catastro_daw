<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Recuperar Contraseña
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Te enviaremos un enlace para restablecer tu contraseña
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="tu@email.com">
            @error('email')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
            Enviar Enlace de Recuperación
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center;">
        <a href="{{ route('login') }}" style="color: #6b7280; font-size: 14px; text-decoration: none;">
            ← Volver al inicio de sesión
        </a>
    </div>
</x-guest-layout>