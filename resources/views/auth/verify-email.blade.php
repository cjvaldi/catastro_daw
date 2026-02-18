<x-guest-layout>
    <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 8px; text-align: center;">
        Verifica tu Email
    </h2>
    <p style="text-align: center; color: #6b7280; margin-bottom: 24px; font-size: 14px;">
        Confirma tu dirección de correo electrónico
    </p>

    <div class="info-box info-box-yellow" style="margin-bottom: 24px;">
        <p style="margin: 0;">
            Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección de correo 
            haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el email, 
            con gusto te enviaremos otro.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success" style="margin-bottom: 16px;">
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                Reenviar Email de Verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 12px;">
                Cerrar Sesión
            </button>
        </form>
    </div>
</x-guest-layout>