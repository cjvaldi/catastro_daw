<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Confirmar Contraseña
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Esta es un área protegida. Confirma tu contraseña para continuar.
    </p>

    <div class="info-box info-box-blue" style="margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px;">
            Por tu seguridad, confirma tu contraseña antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password"
                class="form-input"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Tu contraseña actual"
                autofocus>
            @error('password')
            <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                {{ $message }}
            </small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
            Confirmar
        </button>
    </form>
</x-guest-layout>