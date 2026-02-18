<<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Iniciar Sesión
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Accede a tu cuenta para gestionar tus propiedades
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                   autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Remember Me -->
        <div style="margin-bottom: 16px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" 
                       name="remember" 
                       style="margin-right: 8px; cursor: pointer;">
                <span style="font-size: 14px; color: #4b5563;">
                    Recordarme
                </span>
            </label>
        </div>

        <!-- Actions -->
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                Iniciar Sesión
            </button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   style="text-align: center; color: #6b7280; font-size: 14px; text-decoration: none;">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>
    </form>

    <!-- Register Link -->
    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 12px;">
            ¿No tienes una cuenta?
        </p>
        <a href="{{ route('register') }}" class="btn btn-secondary" style="display: inline-block;">
            Registrarse
        </a>
    </div>
</x-guest-layout>