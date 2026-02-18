<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Crear Cuenta
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Regístrate para acceder a todas las funcionalidades
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Nombre Completo</label>
            <input id="name" 
                   class="form-input" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Juan Pérez">
            @error('name')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="tu@email.com">
            @error('email')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" 
                   class="form-input" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="Mínimo 8 caracteres">
            @error('password')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input id="password_confirmation" 
                   class="form-input" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Repite tu contraseña">
            @error('password_confirmation')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Info -->
        <div class="info-box info-box-blue" style="margin-bottom: 16px; font-size: 13px;">
            Al registrarte tendrás una cuenta <strong>Visitante</strong>. 
            Podrás mejorar a <strong>Premium</strong> desde tu perfil.
        </div>

        <!-- Actions -->
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
            Crear Cuenta
        </button>
    </form>

    <!-- Login Link -->
    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 12px;">
            ¿Ya tienes una cuenta?
        </p>
        <a href="{{ route('login') }}" class="btn btn-secondary" style="display: inline-block;">
            Iniciar Sesión
        </a>
    </div>
</x-guest-layout>