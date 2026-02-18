<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Restablecer Contraseña
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Introduce tu nueva contraseña
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   readonly
                   style="background: #f3f4f6;">
            @error('email')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Nueva Contraseña</label>
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
            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
            <input id="password_confirmation" 
                   class="form-input" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="Repite tu nueva contraseña">
            @error('password_confirmation')
                <small style="color: #ef4444; font-size: 12px; display: block; margin-top: 4px;">
                    {{ $message }}
                </small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
            Restablecer Contraseña
        </button>
    </form>
</x-guest-layout>
